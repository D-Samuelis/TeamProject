export const Toolbar = {
    get el() { return document.getElementById('main-toolbar'); },
    get left() { return document.getElementById('toolbar-left'); },
    get center() { return document.getElementById('toolbar-center'); },
    get right() { return document.getElementById('toolbar-right'); },

    setActions(config) {
        if (!this.el) {
            console.warn('Toolbar base (#main-toolbar) not found in DOM.');
            return;
        }

        this.clear();
        
        if (config.left) this.left.innerHTML = config.left;
        if (config.center) this.center.innerHTML = config.center;
        if (config.right) this.right.innerHTML = config.right;

        this.show();
    },

    show() {
        this.el.classList.add('is-active');
    },

    hide() {
        this.el.classList.remove('is-active');
    },

    clear() {
        // Safe check pred mazaním
        if (this.left) this.left.innerHTML = '';
        if (this.center) this.center.innerHTML = '';
        if (this.right) this.right.innerHTML = '';
    }
};