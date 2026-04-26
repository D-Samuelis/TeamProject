import { Toolbar } from '../../components/toolbar/Toolbar.js';

export function initToolbar() {
    console.log("toolbar init");
    Toolbar.setActions({
        left: `
            <div class="toolbar__status-filters">
                <i class="fa-solid fa-filter"></i>
            </div>
        `,
        right: `
            <button class="btn btn-sm btn-outline-secondary js-modify-asset">
                <i class="fa-solid fa-pen"></i> Modify
            </button>
            <button class="btn btn-sm btn-primary js-create-asset">
                <i class="fa-solid fa-plus"></i> Create Asset
            </button>
            <div class="vr mx-2"></div> <button class="btn btn-sm btn-danger js-archive-asset">
                <i class="fa-solid fa-trash"></i>
            </button>
        `
    });
}