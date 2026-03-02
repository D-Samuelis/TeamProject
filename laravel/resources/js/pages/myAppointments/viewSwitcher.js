export function initViewSwitcher() {
    const views = {
        timeline: {
            btn: document.getElementById('showTimeline'),
            container: document.getElementById('timelineView')
        },
        list: {
            btn: document.getElementById('showList'),
            container: document.getElementById('listView')
        }
    };

    if (!views.timeline.btn || !views.list.btn) return;

    function switchView(targetKey) {
        Object.keys(views).forEach(key => {
            const isTarget = key === targetKey;
            
            views[key].container?.classList.toggle('hidden', !isTarget);
            views[key].btn?.classList.toggle('active', isTarget);
        });

        if (targetKey === 'timeline') {
            window.dispatchEvent(new Event('resize'));
        }
    }

    views.timeline.btn.addEventListener('click', () => switchView('timeline'));
    views.list.btn.addEventListener('click', () => switchView('list'));
}