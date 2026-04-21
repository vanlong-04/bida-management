<template>
    <div>
        <!-- Cấu hình giá giờ bàn -->
        <div class="card-organic card-organic-xl" style="margin-bottom: 32px; border: 1px solid rgba(74,93,78,0.08); background: linear-gradient(135deg, rgba(74,93,78,0.02) 0%, transparent 100%);">
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 24px 28px; border-bottom: 1px solid var(--natural-border);">
                <div>
                    <h3 class="serif" style="font-size: 1.5rem; font-weight: 300; font-style: italic; color: var(--natural-primary); margin: 0;">⚙️ Cấu hình giá giờ bàn</h3>
                    <p class="label-xs" style="margin-top: 4px; color: var(--natural-muted);">Quản lý đơn giá cho bàn Thường và VIP</p>
                </div>
            </div>
            <div style="padding: 28px;">
                <form @submit.prevent="saveHourlyRates">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 28px; margin-bottom: 24px;">
                        <!-- Bàn Thường -->
                        <div class="price-input-group">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                <span class="badge-organic" style="background: rgba(34,197,94,0.1); color: #22c55e; padding: 6px 12px; border-radius: var(--radius-full); font-size: 11px; font-weight: 700; text-transform: uppercase;">Bàn Thường</span>
                            </div>
                            <label class="label-xs" style="display: block; margin-bottom: 6px; font-weight: 600;">Giá 1 giờ</label>
                            <div style="display: flex; align-items: center; gap: 4px;">
                                <input 
                                    type="number" 
                                    class="form-control" 
                                    v-model.number="hourlyRates.thuong" 
                                    min="0" 
                                    step="1000" 
                                    required
                                    style="flex: 1; font-size: 1.2rem; font-weight: 700; padding: 12px 16px;">
                                <span style="font-weight: 700; color: var(--natural-muted); min-width: 50px;">VNĐ</span>
                            </div>
                            <p class="label-xs" style="margin-top: 8px; color: var(--natural-muted);">{{ formatPrice(hourlyRates.thuong) }}</p>
                        </div>

                        <!-- Bàn VIP -->
                        <div class="price-input-group">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                <span class="badge-organic" style="background: rgba(249,115,22,0.1); color: #f97316; padding: 6px 12px; border-radius: var(--radius-full); font-size: 11px; font-weight: 700; text-transform: uppercase;">Bàn VIP</span>
                            </div>
                            <label class="label-xs" style="display: block; margin-bottom: 6px; font-weight: 600;">Giá 1 giờ</label>
                            <div style="display: flex; align-items: center; gap: 4px;">
                                <input 
                                    type="number" 
                                    class="form-control" 
                                    v-model.number="hourlyRates.vip" 
                                    min="0" 
                                    step="1000" 
                                    required
                                    style="flex: 1; font-size: 1.2rem; font-weight: 700; padding: 12px 16px;">
                                <span style="font-weight: 700; color: var(--natural-muted); min-width: 50px;">VNĐ</span>
                            </div>
                            <p class="label-xs" style="margin-top: 8px; color: var(--natural-muted);">{{ formatPrice(hourlyRates.vip) }}</p>
                        </div>
                    </div>

                    <!-- Chênh lệch giá -->
                    <div style="background: rgba(74,93,78,0.04); border-radius: var(--radius-lg); padding: 16px; margin-bottom: 24px;">
                        <p class="label-xs" style="margin: 0 0 4px 0; color: var(--natural-muted);">Chênh lệch VIP vs Thường</p>
                        <p class="font-mono" style="margin: 0; font-size: 1.2rem; font-weight: 700; color: var(--natural-primary);">
                            +{{ formatPrice(Math.max(0, hourlyRates.vip - hourlyRates.thuong)) }}/giờ
                        </p>
                    </div>

                    <!-- Button lưu -->
                    <button type="submit" class="btn-organic btn-primary-organic" :disabled="savingRates" style="width: 100%; padding: 14px; font-size: 0.95rem; font-weight: 600;">
                        <span v-if="savingRates">
                            <i class="fa-solid fa-spinner fa-spin"></i> Đang lưu...
                        </span>
                        <span v-else>
                            <i class="fa-solid fa-save"></i> Lưu cấu hình
                        </span>
                    </button>

                    <!-- Message -->
                    <transition name="fade">
                        <div 
                            v-if="rateMessage" 
                            :class="[
                                'alert', 
                                rateMessage.includes('Lỗi') ? 'alert-danger' : 'alert-success'
                            ]"
                            style="margin-top: 12px;">
                            {{ rateMessage }}
                        </div>
                    </transition>
                </form>
            </div>
        </div>
        <!-- Header -->
        <header class="page-header">
            <div>
                <h2 class="page-title">Quản lý <strong>Bàn</strong></h2>
                <p class="page-subtitle">Cấu hình sơ đồ bàn và loại phòng của quán.</p>
            </div>
            <button class="btn-organic btn-primary-organic" style="padding: 16px 36px; font-size: 11px;" data-bs-toggle="modal" data-bs-target="#addTableModal">
                <i class="fa-solid fa-plus"></i>
                THÊM BÀN MỚI
            </button>
        </header>

        <!-- Table Card -->
        <div class="card-organic card-organic-xl">
            <!-- Search/Filter Bar -->
            <div class="filter-bar">
                <div class="search-organic-wrap">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" class="input-organic" style="padding-left: 52px;" placeholder="Tìm kiếm bàn..." v-model="searchQuery">
                </div>
                <div style="display: flex; gap: 12px;">
                    <button class="btn-organic btn-ghost-organic" style="padding: 12px;">
                        <i class="fa-solid fa-filter"></i>
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div style="overflow-x: auto;">
                <table class="table-organic">
                    <thead>
                        <tr>
                            <th>Tên Bàn</th>
                            <th>Loại bàn</th>
                            <th>Giá giờ</th>
                            <th>Trạng thái</th>
                            <th style="text-align: right;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(value, index) in filteredTables" :key="index">
                            <td>
                                <span class="table-name serif">{{ value.ban_name }}</span>
                            </td>
                            <td>
                                <span class="table-type">
                                    <span v-if="Number(value.loai_ban) === 1">Thường</span>
                                    <span v-else>VIP</span>
                                </span>
                            </td>
                            <td>
                                <span class="font-mono" style="font-weight: 700; color: var(--natural-primary);">
                                    {{ formatPrice(value.hourly_rate || 0) }}/giờ
                                </span>
                            </td>
                            <td>
                                <span v-if="value.status == 1" class="badge-organic badge-success-organic">
                                    <span class="dot dot-sm dot-success dot-pulse"></span>
                                    Trống
                                </span>
                                <span v-else class="badge-organic badge-danger-organic">
                                    <span class="dot dot-sm dot-danger"></span>
                                    Đang sử dụng
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div class="action-cell" style="display: flex; align-items: center; justify-content: flex-end; gap: 10px;">
                                    <button class="btn-organic btn-ghost-organic" style="padding: 10px;" data-bs-toggle="modal"
                                        data-bs-target="#updateTableModal"
                                        v-on:click="Object.assign(update_table, value)">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn-organic btn-danger-organic" style="padding: 10px;" data-bs-toggle="modal"
                                        data-bs-target="#deleteTableModal"
                                        v-on:click="Object.assign(delete_table, value)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredTables.length === 0">
                            <td colspan="5" style="text-align: center; padding: 60px 40px;">
                                <i class="fa-solid fa-inbox" style="font-size: 2rem; color: var(--natural-muted); opacity: 0.3; display: block; margin-bottom: 12px;"></i>
                                <span style="color: var(--natural-muted); font-style: italic;">Không tìm thấy bàn nào...</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Thêm -->
        <div class="modal fade" id="addTableModal" tabindex="-1" aria-labelledby="addTableModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="addTableModalLabel">Thêm bàn</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="mb-3">
                                <label for="table_name" class="form-label">Tên bàn</label>
                                <input type="text" class="form-control" id="table_name" v-model="create_table.ban_name">
                            </div>
                            <div class="mb-3">
                                <label for="table_type" class="form-label">Loại bàn</label>
                                <select class="form-select" id="table_type" v-model="create_table.loai_ban">
                                    <option selected :value="1">Thường</option>
                                    <option :value="2">VIP</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="table_status" class="form-label">Trạng thái</label>
                                <select class="form-select" id="table_status" v-model="create_table.status">
                                    <option selected value="1">Trống</option>
                                    <option value="2">Đang sử dụng</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                            v-on:click="createTable()">Thêm</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Sửa -->
        <div class="modal fade" id="updateTableModal" tabindex="-1" aria-labelledby="updateTableModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="updateTableModalLabel">Cập nhật bàn</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="mb-3">
                                <label for="table_name" class="form-label">Tên bàn</label>
                                <input type="text" class="form-control" id="table_name" v-model="update_table.ban_name">
                            </div>
                            <div class="mb-3">
                                <label for="table_type" class="form-label">Loại bàn</label>
                                <select class="form-select" id="table_type" v-model="update_table.loai_ban">
                                    <option selected :value="1">Thường</option>
                                    <option :value="2">VIP</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="table_status" class="form-label">Trạng thái</label>
                                <select class="form-select" id="table_status" v-model="update_table.status">
                                    <option selected value="1">Trống</option>
                                    <option value="2">Đang sử dụng</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" v-on:click="updateTable()">Cập nhật</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Xóa -->
        <div class="modal fade" id="deleteTableModal" tabindex="-1" aria-labelledby="deleteTableModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="deleteTableModalLabel">Xóa bàn</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger" role="alert">
                            <p> Bạn có chắc chắn muốn xóa bàn {{ delete_table.ban_name }} không?</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                            v-on:click="deleteTable()">Xóa</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from 'axios'
export default {
    data() {
        return {
            tables: [],
            searchQuery: '',
            create_table: {
                ban_name: '',
                loai_ban: 1,
                status: 1,
            },
            update_table: {
                ban_name: '',
                loai_ban: 1,
                status: 1,
            },
            delete_table: {
                ban_name: '',
                loai_ban: 1,
                status: 1,
            }
            ,
            hourlyRates: { thuong: 50000, vip: 70000 },
            savingRates: false,
            rateMessage: '',
        }
    },
    computed: {
        filteredTables() {
            if (!this.searchQuery) return this.tables;
            const q = this.searchQuery.toLowerCase();
            return this.tables.filter(t => t.ban_name && t.ban_name.toLowerCase().includes(q));
        }
    },
    mounted() {
        this.getTables();
        this.getHourlyRates();
    },
    methods: {
        getHourlyRates() {
            axios.get('http://127.0.0.1:8000/api/admin/bida-config/get-hourly-rates')
                .then(res => {
                    if (res.data) this.hourlyRates = res.data;
                })
                .catch(() => { this.rateMessage = 'Không lấy được giá giờ hiện tại!'; });
        },
        saveHourlyRates() {
            this.savingRates = true;
            this.rateMessage = '';
            axios.post('http://127.0.0.1:8000/api/admin/bida-config/set-hourly-rates', this.hourlyRates)
                .then(res => {
                    this.rateMessage = res.data.message || 'Đã lưu giá giờ!';
                })
                .catch(() => { this.rateMessage = 'Lỗi khi lưu giá giờ!'; })
                .finally(() => { this.savingRates = false; });
        },
        formatPrice(price) {
            return new Intl.NumberFormat('vi-VN').format(Number(price) || 0) + 'đ';
        },
        getTables() {
            axios.get('http://127.0.0.1:8000/api/admin/ban/get-data')
                .then((res) => {
                    console.log('API response:', res.data);
                    this.tables = res.data.data;
                })
                .catch((error) => {
                    console.error('API error:', error);
                })
        },
        createTable() {
            axios.post('http://127.0.0.1:8000/api/admin/ban/create-data', this.create_table)
                .then((res) => {
                    if (res.data.status) {
                        this.getTables();
                        this.create_table = {
                            ban_name: '',
                            loai_ban: 1,
                            status: 1,
                        }
                        alert(res.data.message);
                    } else {
                        alert(res.data.message);
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        updateTable() {
            axios.post('http://127.0.0.1:8000/api/admin/ban/update-data', this.update_table)
                .then((res) => {
                    if (res.data.status) {
                        this.getTables();
                        alert(res.data.message);
                    } else {
                        alert(res.data.message);
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        deleteTable() {
            axios.post('http://127.0.0.1:8000/api/admin/ban/delete-data', this.delete_table)
                .then((res) => {
                    if (res.data.status) {
                        this.getTables();
                        alert(res.data.message);
                    } else {
                        alert(res.data.message);
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        },
    }
}
</script>
<style scoped>
.table-name {
    font-size: 1.4rem;
    font-weight: 300;
    font-style: italic;
    letter-spacing: -0.02em;
    color: var(--natural-text);
    transition: color var(--transition-fast);
}

tr:hover .table-name {
    color: var(--natural-primary);
}

.table-type {
    font-size: 14px;
    color: var(--natural-muted);
    font-weight: 500;
    letter-spacing: 0.05em;
}
</style>