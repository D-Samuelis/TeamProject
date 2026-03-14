<style>
    .modal-backdrop { position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center; }
    .modal-box      { background:#fff;border-radius:8px;padding:2rem;width:100%;max-width:580px;max-height:90vh;overflow-y:auto;position:relative; }
    .modal-close    { position:absolute;top:1rem;right:1rem;background:none;border:none;font-size:1.25rem;cursor:pointer; }
    .btn-primary    { padding:8px 16px;background:#1a1a1a;color:#fff;border:none;border-radius:4px;cursor:pointer; }
    .btn-secondary  { padding:8px 16px;background:#fff;border:1px solid #ccc;border-radius:4px;cursor:pointer; }
</style>
<script>
    function openModal(id)  { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }
    document.querySelectorAll('.modal-backdrop').forEach(el => {
        el.addEventListener('click', e => { if (e.target === el) el.style.display = 'none'; });
    });
</script>
