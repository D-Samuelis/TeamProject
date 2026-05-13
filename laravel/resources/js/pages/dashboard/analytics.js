export function initDashboardAnalytics() {
    const data = window.BE_DATA ?? {};
    const {
        businesses = [],
        allBranches = [],
        allServices = [],
        allAssets = [],
    } = data;

    if (businesses.length === 0) return;

    const charts = {};
    const baseChart = {
        toolbar: { show: false },
        fontFamily: 'inherit',
        animations: { enabled: true, speed: 350 },
    };

    const setText = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    };

    const renderChart = (selector, options) => {
        const el = document.querySelector(selector);
        if (!el || typeof ApexCharts === 'undefined') return null;
        const chart = new ApexCharts(el, options);
        chart.render();
        return chart;
    };

    const donutOptions = (series, labels) => ({
        chart: { ...baseChart, type: 'donut', height: 240 },
        series,
        labels,
        colors: ['#22c55e', '#f59e0b', '#ef4444'],
        legend: { position: 'bottom', fontSize: '12px' },
        dataLabels: { enabled: true, style: { fontSize: '11px' } },
        plotOptions: { pie: { donut: { size: '55%' } } },
    });

    // --- Logic ---
    function computeStats(bizList, branchList, svcList, assetList) {
        return {
            biz: {
                total:     bizList.length,
                published: bizList.filter(b => !b.deleted_at &&  b.is_published).length,
                hidden:    bizList.filter(b => !b.deleted_at && !b.is_published).length,
                archived:  bizList.filter(b =>  b.deleted_at).length,
            },
            br: {
                total:    branchList.length,
                active:   branchList.filter(b => !b.deleted_at &&  b.is_active).length,
                inactive: branchList.filter(b => !b.deleted_at && !b.is_active).length,
                archived: branchList.filter(b =>  b.deleted_at).length,
            },
            svc: {
                total:    svcList.length,
                active:   svcList.filter(s => !s.deleted_at &&  s.is_active).length,
                inactive: svcList.filter(s => !s.deleted_at && !s.is_active).length,
                archived: svcList.filter(s =>  s.deleted_at).length,
            },
            ast: {
                total:    assetList.length,
                active:   assetList.filter(a => !a.deleted_at &&  a.is_active).length,
                inactive: assetList.filter(a => !a.deleted_at && !a.is_active).length,
                archived: assetList.filter(a =>  a.deleted_at).length,
            },
        };
    }

    function filterByBusiness(bizId) {
        if (bizId === 'all') {
            return { bizList: businesses, branchList: allBranches, svcList: allServices, assetList: allAssets };
        }
        const id = parseInt(bizId);
        const branchList = allBranches.filter(b => b.business_id === id || b.business?.id === id);
        const branchIds = new Set(branchList.map(b => b.id));
        const svcIds = new Set(branchList.flatMap(b => (b.services ?? []).map(s => s.id)));
        const svcList = allServices.filter(s => svcIds.has(s.id));
        const assetList = allAssets.filter(a => branchIds.has(a.branch_id));

        return { bizList: businesses.filter(b => b.id === id), branchList, svcList, assetList };
    }

    function updateUI(bizId, filtered) {
        const stats = computeStats(filtered.bizList, filtered.branchList, filtered.svcList, filtered.assetList);
        
        Object.keys(stats).forEach(key => {
            Object.keys(stats[key]).forEach(statType => {
                setText(`s-${key}-${statType}`, stats[key][statType]);
            });
        });

        setText('bizInfoBranches', `${filtered.branchList.length} branches`);
        setText('bizInfoServices', `${filtered.svcList.length} services`);
        setText('bizInfoAssets',   `${filtered.assetList.length} assets`);

        const statusWrap = document.getElementById('bizInfoStatusWrap');
        const statusEl = document.getElementById('bizInfoStatus');
        const bizGroup = document.getElementById('group-businesses');
        
        if (bizGroup) bizGroup.style.display = bizId === 'all' ? '' : 'none';

        if (bizId === 'all') {
            statusWrap.style.display = 'none';
        } else {
            const biz = businesses.find(b => b.id === parseInt(bizId));
            if (biz && statusWrap && statusEl) {
                statusWrap.style.display = '';
                if (biz.deleted_at) { statusEl.textContent = 'Archived'; statusEl.className = 'biz-selector__badge--red'; }
                else if (biz.is_published) { statusEl.textContent = 'Published'; statusEl.className = 'biz-selector__badge--green'; }
                else { statusEl.textContent = 'Hidden'; statusEl.className = 'biz-selector__badge--amber'; }
            }
        }

        charts.bizStatus?.updateSeries([stats.biz.published, stats.biz.hidden, stats.biz.archived]);
        charts.brStatus?.updateSeries([stats.br.active, stats.br.inactive, stats.br.archived]);
        charts.svcStatus?.updateSeries([stats.svc.active, stats.svc.inactive, stats.svc.archived]);
        charts.astStatus?.updateSeries([stats.ast.active, stats.ast.inactive, stats.ast.archived]);

        charts.perBiz?.updateOptions({
            series: [
                { name: 'Branches', data: filtered.bizList.map(b => allBranches.filter(br => br.business_id === b.id).length) },
                { name: 'Services', data: filtered.bizList.map(b => {
                    const brs = allBranches.filter(br => br.business_id === b.id);
                    return new Set(brs.flatMap(br => (br.services ?? []).map(s => s.id))).size;
                })},
            ],
            xaxis: { categories: filtered.bizList.map(b => b.name) },
        });

        charts.coverage?.updateOptions({
            series: [
                { name: 'Linked',    data: filtered.branchList.map(b => b.services?.length ?? 0) },
                { name: 'Available', data: filtered.branchList.map(() => filtered.svcList.length) },
            ],
            xaxis: { categories: filtered.branchList.map(b => b.name) },
        });
    }

    const s = computeStats(businesses, allBranches, allServices, allAssets);

    charts.bizStatus = renderChart('#chart-business-status', donutOptions([s.biz.published, s.biz.hidden, s.biz.archived], ['Published', 'Hidden', 'Archived']));
    charts.brStatus = renderChart('#chart-branch-status', donutOptions([s.br.active, s.br.inactive, s.br.archived], ['Active', 'Inactive', 'Archived']));
    charts.svcStatus = renderChart('#chart-service-status', donutOptions([s.svc.active, s.svc.inactive, s.svc.archived], ['Active', 'Inactive', 'Archived']));
    charts.astStatus = renderChart('#chart-asset-status', donutOptions([s.ast.active, s.ast.inactive, s.ast.archived], ['Active', 'Inactive', 'Archived']));

    charts.perBiz = renderChart('#chart-per-business', {
        chart: { ...baseChart, type: 'bar', height: 280 },
        series: [{ name: 'Branches', data: [] }, { name: 'Services', data: [] }],
        xaxis: { categories: [] },
        colors: ['#3b82f6', '#a855f7'],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
    });

    charts.coverage = renderChart('#chart-service-coverage', {
        chart: { ...baseChart, type: 'bar', height: 280 },
        series: [{ name: 'Linked', data: [] }, { name: 'Available', data: [] }],
        xaxis: { categories: [] },
        colors: ['#14b8a6', '#e5e7eb'],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
    });

    updateUI('all', { bizList: businesses, branchList: allBranches, svcList: allServices, assetList: allAssets });

    const bizSelect = document.getElementById('bizSelect');
    bizSelect?.addEventListener('change', e => {
        const filtered = filterByBusiness(e.target.value);
        updateUI(e.target.value, filtered);
    });
}