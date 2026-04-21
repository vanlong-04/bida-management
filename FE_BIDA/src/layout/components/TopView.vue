<template>
    <header class="user-header">
        <div class="user-header-left">
            <div class="user-header-tabs">
                <router-link to="/user/dat-ban" class="user-tab" :class="{ active: $route.path === '/user/dat-ban' }">
                    <i class="fa-solid fa-table-cells-large"></i>
                    <span class="user-tab-label">Phòng bàn</span>
                </router-link>
                <router-link to="/user/thuc-don" class="user-tab" :class="{ active: $route.path === '/user/thuc-don' }">
                    <i class="fa-solid fa-utensils"></i>
                    <span class="user-tab-label">Thực đơn</span>
                </router-link>
            </div>
        </div>
        <div class="user-header-right">
            <div class="user-header-info">
                <p class="user-header-name">Nhân viên</p>
                <p class="label-xs">Lễ tân - Ca sáng</p>
            </div>
            <div class="user-header-avatar">NV</div>
            <button class="user-header-menu-btn" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasNavbar">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar">
                <div class="offcanvas-header" style="border-bottom: 1px solid rgba(245,245,244,1); padding: 24px 28px;">
                    <h5 class="offcanvas-title serif" style="font-style: italic; color: var(--natural-primary); font-size: 1.5rem;">Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body" style="padding: 16px;">
                    <ul class="offcanvas-nav">
                        <li><router-link to="/user/dat-ban" class="offcanvas-nav-link"><i class="fa-solid fa-table-cells-large"></i> Phòng bàn</router-link></li>
                        <li><router-link to="/user/thuc-don" class="offcanvas-nav-link"><i class="fa-solid fa-utensils"></i> Thực đơn</router-link></li>
                        <li><router-link to="/admin/dashboard" class="offcanvas-nav-link"><i class="fa-solid fa-chart-pie"></i> Quản lý</router-link></li>
                        <li><router-link to="/admin/ban" class="offcanvas-nav-link"><i class="fa-solid fa-table-cells"></i> Quản lý bàn</router-link></li>
                        <li><router-link to="/admin/dich-vu" class="offcanvas-nav-link"><i class="fa-solid fa-mug-hot"></i> Quản lý dịch vụ</router-link></li>
                        <li style="margin-top: 16px; padding-top: 16px; border-top: 1px solid rgba(245,245,244,1);">
                            <router-link to="/login" class="offcanvas-nav-link" style="color: #ef4444;"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</router-link>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
</template>

<script>
export default {
    data() {
        return {
            selectedTable: null,
        };
    },
    methods: {
        selectTable(table) {
            this.selectedTable = table;
        }
    }
};
</script>

<style scoped>
.user-header {
    height: 72px;
    background-color: var(--natural-surface);
    border-bottom: 1px solid var(--natural-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 24px;
    backdrop-filter: blur(12px);
    position: sticky;
    top: 0;
    z-index: 50;
}

.user-header-left {
    display: flex;
    align-items: center;
}

.user-header-tabs {
    display: flex;
    gap: 4px;
}

.user-tab {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: var(--radius-md);
    text-decoration: none;
    color: var(--natural-muted);
    font-size: 13px;
    font-weight: 600;
    transition: all var(--transition-fast);
}

.user-tab:hover {
    color: var(--natural-primary);
    background-color: rgba(250, 250, 249, 1);
}

.user-tab.active {
    color: var(--natural-primary);
    background-color: var(--natural-accent-light);
}

.user-tab-label {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    font-weight: 700;
}

.user-header-right {
    display: flex;
    align-items: center;
    gap: 16px;
}

.user-header-info {
    text-align: right;
}

.user-header-name {
    font-size: 14px;
    font-weight: 700;
    margin: 0;
    letter-spacing: -0.02em;
}

.user-header-avatar {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-md);
    background-color: rgba(245, 245, 244, 1);
    border: 1px solid rgba(214, 211, 209, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 12px;
    color: var(--natural-muted);
}

.user-header-menu-btn {
    padding: 8px;
    background: none;
    border: 1px solid rgba(214, 211, 209, 1);
    border-radius: var(--radius-sm);
    color: var(--natural-muted);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.user-header-menu-btn:hover {
    color: var(--natural-primary);
    border-color: var(--natural-primary);
}

.offcanvas-nav {
    list-style: none;
    padding: 0;
    margin: 0;
}

.offcanvas-nav li {
    margin-bottom: 4px;
}

.offcanvas-nav-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: var(--radius-md);
    text-decoration: none;
    color: var(--natural-muted);
    font-size: 13px;
    font-weight: 600;
    transition: all var(--transition-fast);
}

.offcanvas-nav-link:hover {
    color: var(--natural-primary);
    background-color: rgba(250, 250, 249, 1);
}

.offcanvas-nav-link i {
    width: 20px;
    text-align: center;
}
</style>
