import re

TOOL_ENTITY_MAP = {
    "list-services-tool":   "service",
    "list-businesses-tool": "business",
    "list-branches-tool":   "branch",
}

STATIC_NAVIGATIONS = {
    "list-appointments-tool": {
        "type":  "appointments",
        "id":    "appointments",
        "name":  "your appointments",
        "url":   "/my-appointments",
        "label": "View My Appointments",
    },
    "make-appointment-tool": {
        "type":  "appointments",
        "id":    "appointments",
        "name":  "your appointments",
        "url":   "/my-appointments",
        "label": "View My Appointments",
    },
}


def _parse_entities(result_text: str, entity_type: str) -> list[dict]:
    stripped = result_text.strip()
    record_boundary = re.compile(rf'(?={re.escape(entity_type)} id\s*:)', re.IGNORECASE)
    raw_records = [r.strip() for r in record_boundary.split(stripped) if r.strip()]
    if not raw_records:
        raw_records = [stripped]

    entities = []
    kv_pattern = re.compile(r'[\w\s]+:\s*[^,\n]+')

    for record in raw_records:
        pairs: dict[str, str] = {}
        for match in kv_pattern.finditer(record):
            token = match.group(0).strip().rstrip(",")
            if ":" not in token:
                continue
            key, _, value = token.partition(":")
            key = re.sub(rf'^{re.escape(entity_type)} ', "", key.strip().lower())
            pairs[key] = value.strip()

        if eid := pairs.get("id"):
            entities.append({
                "id":          eid,
                "name":        pairs.get("name") or eid,
                "business_id": pairs.get("business_id"),
            })

    return entities


def _build_url(entity_type: str, entity_id: str, business_id_ctx: str | None) -> str | None:
    """Build the frontend URL for an entity."""
    match entity_type:
        case "business":
            return f"/book/business/{entity_id}"
        case "service":
            if business_id_ctx is None:
                return None
            return f"/book/business/{business_id_ctx}/service/{entity_id}"
        case "branch":
            if business_id_ctx is None:
                return None
            return f"/book/business/{business_id_ctx}?branch_id={entity_id}"
        case _:
            return None


def build_navigations(steps: list[dict]) -> list[dict]:
    seen: dict[tuple, dict] = {}

    for step in steps:
        tool_name = step.get("tool", "")

        if tool_name in STATIC_NAVIGATIONS:
            nav = STATIC_NAVIGATIONS[tool_name]
            seen[(nav["type"], nav["id"])] = nav
            continue

        entity_type = TOOL_ENTITY_MAP.get(tool_name)
        if not entity_type:
            continue

        for entity in _parse_entities(step.get("result", ""), entity_type):
            eid = entity["id"]
            url = _build_url(entity_type, eid, entity.get("business_id"))
            if url is None:
                continue

            seen[(entity_type, eid)] = {
                "type":  entity_type,
                "id":    eid,
                "name":  entity["name"],
                "url":   url,
                "label": f"View {entity['name']}",
            }

    return list(seen.values())