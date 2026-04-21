<template>
    <div>
        <!-- Stats Row -->
        <div class="uv-stats">
            <div class="uv-stat-card uv-stat-total">
                <div class="uv-stat-content">
                    <p class="uv-stat-label">Tổng số bàn</p>
                    <h3 class="uv-stat-value">{{ tableList.length }}</h3>
                </div>
                <i class="fa-solid fa-table-cells uv-stat-icon"></i>
            </div>
            <div class="uv-stat-card uv-stat-empty">
                <div class="uv-stat-content">
                    <p class="uv-stat-label">Bàn trống</p>
                    <h3 class="uv-stat-value">{{ tableList.filter(b => b.status === 1).length }}</h3>
                </div>
                <i class="fa-solid fa-circle-check uv-stat-icon"></i>
            </div>
            <div class="uv-stat-card uv-stat-used">
                <div class="uv-stat-content">
                    <p class="uv-stat-label">Đang sử dụng</p>
                    <h3 class="uv-stat-value">{{ tableList.filter(b => b.status === 2).length }}</h3>
                </div>
                <i class="fa-solid fa-circle-xmark uv-stat-icon"></i>
            </div>
        </div>

        <!-- Filter -->
        <div class="uv-filter-bar">
            <h3 class="uv-section-title serif">Danh sách bàn</h3>
            <div class="uv-filter-btns">
                <button class="uv-filter-btn" :class="{ active: activeType === null }" @click="filterByType(null)">Tất cả</button>
                <button class="uv-filter-btn" :class="{ active: activeType === 1 }" @click="filterByType(1)">Thường</button>
                <button class="uv-filter-btn" :class="{ active: activeType === 2 }" @click="filterByType(2)">VIP</button>
            </div>
            <div style="display: flex; gap: 10px;">
                <button class="uv-filter-btn" :class="{ 'btn-warning': selectionMode }" @click="toggleSelectionMode">
                    <i class="fa-solid fa-list-check"></i> {{ selectionMode ? 'Hủy chọn' : 'Chọn nhiều bàn' }}
                </button>
                <button v-if="selectionMode" class="uv-filter-btn active" @click="bookSelectedTables" :disabled="selectedTableIds.length === 0 || loading">
                    <i class="fa-solid fa-play"></i> Mở {{ selectedTableIds.length }} bàn
                </button>
            </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" style="text-align:center; padding: 40px;">
            <div class="spinner-border" style="color: var(--natural-primary);"></div>
        </div>

        <!-- Table Grid -->
        <div v-else class="uv-grid">
            <button
                v-for="ban in filteredTables"
                :key="ban.id"
                class="uv-table-card"
                :class="{
                    'uv-table-used': ban.status === 2,
                    'uv-table-selected': selectedId === ban.id || selectedTableIds.includes(ban.id),
                    'uv-table-opening': openingId === ban.id
                }"
                @click="handleTableClick(ban)"
                :disabled="openingId === ban.id"
            >
                <!-- Header -->
                <div class="uv-table-header">
                    <span class="uv-table-name serif">{{ ban.name }}</span>
                    <span class="dot" :class="ban.status === 1 ? 'dot-success' : 'dot-danger'"></span>
                </div>

                <!-- Footer -->
                <div class="uv-table-footer">
                    <p class="label-xs" style="margin-bottom: 6px;">
                        <span>{{ ban.loai_ban_label || (ban.loai_ban === 2 ? 'VIP' : 'Thường') }}</span>
                    </p>
                    <p class="label-xs font-mono" style="margin-bottom: 8px; color: var(--natural-primary); letter-spacing: 0.12em;">
                        {{ formatPrice(ban.hourly_rate || 0) }}/giờ
                    </p>
                    <div v-if="openingId === ban.id" class="uv-table-status-used">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                        <span>Đang mở...</span>
                    </div>
                    <div v-else-if="ban.status === 2" class="uv-table-status-used">
                        <i class="fa-solid fa-clock uv-pulse"></i>
                        <span>Đang sử dụng</span>
                    </div>
                    <div v-else class="uv-table-status-free">
                        <span class="dot dot-sm dot-success dot-pulse"></span>
                        Nhấn để mở bàn
                    </div>
                </div>

                <!-- Hover overlay -->
                <div class="uv-table-overlay" :class="{ 'uv-table-overlay-view': ban.status === 2 }">
                    <i :class="ban.status === 2 ? 'fa-solid fa-eye' : 'fa-solid fa-play'" class="uv-overlay-icon"></i>
                    <span class="uv-overlay-label">{{ ban.status === 2 ? 'Xem Bill' : 'Mở Bàn' }}</span>
                </div>
            </button>
        </div>

        <!-- MODAL: Mở bàn nhanh (Duration + Combo) -->
        <div class="modal fade" id="quickOpenModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title serif">Mở bàn: {{ tableToOpen?.name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Duration -->
                        <div class="mb-4">
                            <label class="label-xs mb-2 d-block">CHỌN THỜI GIAN CHƠI</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <button v-for="d in durations" :key="d.value" 
                                    class="uv-filter-btn" :class="{ active: selectedDuration === d.value }"
                                    @click="selectedDuration = d.value">
                                    {{ d.label }}
                                </button>
                            </div>
                        </div>

                        <!-- Combo -->
                        <div class="mb-3">
                            <label class="label-xs mb-2 d-block">CHỌN COMBO (NẾU CÓ)</label>
                            <select class="form-select" v-model="selectedComboId">
                                <option :value="null">Không chọn combo</option>
                                <option v-for="c in combos" :key="c.dich_vu_id" :value="c.dich_vu_id">
                                    {{ c.dich_vu_name }} ({{ formatPrice(c.price) }})
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-primary" @click="confirmOpenTable" :disabled="openingId === tableToOpen?.id">
                            <i class="fa-solid fa-play"></i> BẮT ĐẦU CHƠI
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { Modal } from 'bootstrap';
export default {
    props: {
        selectedBan: { type: Object, default: null }
    },
    data() {
        return {
            tableList: [],
            filteredTables: [],
            loading: false,
            activeType: null,
            selectedId: null,
            openingId: null, // ID bàn đang trong quá trình mở
            selectionMode: false,
            selectedTableIds: [],
            tableToOpen: null,
            selectedDuration: 0,
            selectedComboId: null,
            combos: [],
            durations: [
                { label: 'Linh hoạt', value: 0 },
                { label: '1 Giờ', value: 60 },
                { label: '2 Giờ', value: 120 },
                { label: '3 Giờ', value: 180 },
            ]
        }
    },
    mounted() {
        this.getBanData();
    },
    methods: {
        getBanData() {
            this.loading = true;
            axios.get('http://127.0.0.1:8000/api/admin/ban/get-data')
                .then((res) => {
                    if (res.data.data) {
                        this.tableList = res.data.data.map(item => ({
                            id: item.ban_id,
                            name: item.ban_name,
                            loai_ban: Number(item.loai_ban) === 1 ? 1 : 2,
                            loai_ban_label: item.loai_ban_label,
                            hourly_rate: Number(item.hourly_rate || 0),
                            status: item.status,
                        }));
                        this.filterByType(this.activeType);
                    }
                })
                .catch(console.error)
                .finally(() => { this.loading = false; });
        },

        filterByType(loaiBan) {
            this.activeType = loaiBan;
            this.filteredTables = loaiBan
                ? this.tableList.filter(b => b.loai_ban === loaiBan)
                : this.tableList;
        },

        formatPrice(price) {
            return new Intl.NumberFormat('vi-VN').format(Number(price) || 0) + 'đ';
        },

        // ===== LOGIC CHÍNH: Click bàn =====
        handleTableClick(ban) {
            if (this.selectionMode) {
                if (ban.status === 2) {
                    alert('Bàn này đang được sử dụng, không thể chọn.');
                    return;
                }
                const index = this.selectedTableIds.indexOf(ban.id);
                if (index > -1) {
                    this.selectedTableIds.splice(index, 1);
                } else {
                    this.selectedTableIds.push(ban.id);
                }
                return;
            }

            this.selectedId = ban.id;
            if (ban.status === 1) {
                // Bàn trống → Hiện modal chọn thời gian/combo
                this.tableToOpen = ban;
                this.selectedDuration = 0;
                this.selectedComboId = null;
                this.loadCombos();
                const modal = new Modal(document.getElementById('quickOpenModal'));
                modal.show();
            } else {
                // Bàn đang dùng → chọn để xem bill
                this.$emit('select-ban', ban);
            }
        },

        loadCombos() {
            axios.get('http://127.0.0.1:8000/api/admin/dich-vu/get-data')
                .then(res => {
                    this.combos = (res.data.data || []).filter(i => i.loai_dich_vu === 'Combo');
                });
        },

        toggleSelectionMode() {
            this.selectionMode = !this.selectionMode;
            this.selectedTableIds = [];
        },

        bookSelectedTables() {
            if (this.selectedTableIds.length === 0) return;
            this.loading = true;
            axios.post('http://127.0.0.1:8000/api/admin/hoa-don/book-multiple-tables', {
                ban_ids: this.selectedTableIds
            })
            .then(res => {
                alert(res.data.message);
                this.selectionMode = false;
                this.selectedTableIds = [];
                this.getBanData();
            })
            .catch(err => {
                alert('Lỗi khi mở bàn nhóm!');
                console.error(err);
            })
            .finally(() => { this.loading = false; });
        },

        confirmOpenTable() {
            if (!this.tableToOpen) return;
            this.openTable(this.tableToOpen, this.selectedDuration, this.selectedComboId);
            const modalEl = document.getElementById('quickOpenModal');
            const modal = Modal.getInstance(modalEl);
            if (modal) modal.hide();
        },

        // Mở bàn: tạo hóa đơn + cập nhật trạng thái + chọn bàn
        openTable(ban, duration = 0, comboId = null) {
            this.openingId = ban.id;

            // Kiểm tra có HĐ chưa TT chưa
            axios.get(`http://127.0.0.1:8000/api/admin/hoa-don/get-bill-by-ban-id?ban_id=${ban.id}`)
                .then((res) => {
                    const data = res.data.data;
                    const hasUnpaid = data && (
                        (!Array.isArray(data) && data.hoa_don_id) ||
                        (Array.isArray(data) && data.length > 0)
                    );

                    if (hasUnpaid) {
                        // Đã có HĐ → chỉ chọn để xem
                        this.getBanData();
                        this.$emit('select-ban', { ...ban, status: 2 });
                        return;
                    }

                    // Tạo hóa đơn mới
                    return axios.post('http://127.0.0.1:8000/api/admin/hoa-don/create-data', {
                        ban_id: ban.id,
                        nhan_vien_id: 1,
                        start_time: this.getNow(),
                        status: 'chưa thanh toán',
                        duration: duration,
                        combo_id: comboId
                    });
                })
                .then((res) => {
                    if (!res) return; // đã xử lý ở trường hợp hasUnpaid
                    // Cập nhật trạng thái bàn → đang dùng
                    return axios.post('http://127.0.0.1:8000/api/admin/hoa-don/update-status', {
                        ban_id: ban.id,
                        status: 2
                    });
                })
                .then((res) => {
                    if (!res) return;
                    this.getBanData();
                    // Emit để layout hiển thị BanDetail
                    this.$emit('select-ban', { ...ban, status: 2 });
                })
                .catch((error) => {
                    console.error('Lỗi khi mở bàn:', error);
                    alert('Không thể mở bàn. Vui lòng thử lại.');
                })
                .finally(() => {
                    this.openingId = null;
                });
        },

        getNow() {
            const pad = n => n < 10 ? '0' + n : n;
            const d = new Date();
            return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
        }
    }
}
</script>

<style scoped>
.uv-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 28px;
}
.uv-stat-card {
    border-radius: var(--radius-lg);
    padding: 20px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: white;
}
.uv-stat-total { background: var(--natural-primary); box-shadow: 0 4px 15px var(--natural-primary-shadow); }
.uv-stat-empty { background: #059669; box-shadow: 0 4px 15px rgba(5,150,105,0.2); }
.uv-stat-used  { background: #dc2626; box-shadow: 0 4px 15px rgba(220,38,38,0.2); }
.uv-stat-label { font-size: 12px; opacity: 0.85; margin: 0 0 4px 0; }
.uv-stat-value { font-size: 1.75rem; font-weight: 700; margin: 0; }
.uv-stat-icon  { font-size: 2rem; opacity: 0.3; }

.uv-filter-bar {
    display: flex; align-items: center;
    justify-content: space-between; margin-bottom: 24px;
}
.uv-section-title { font-size: 1.5rem; font-weight: 300; font-style: italic; color: var(--natural-text); margin: 0; }
.uv-filter-btns { display: flex; gap: 8px; }
.uv-filter-btn {
    padding: 8px 18px; border: 1px solid var(--natural-border);
    border-radius: var(--radius-full); background: white;
    color: var(--natural-muted); font-family: var(--font-sans);
    font-size: 12px; font-weight: 600; cursor: pointer;
    transition: all var(--transition-fast);
}
.uv-filter-btn:hover { border-color: var(--natural-primary); color: var(--natural-primary); }
.uv-filter-btn.active { background: var(--natural-primary); color: white; border-color: var(--natural-primary); }

.uv-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
}
.uv-table-card {
    position: relative; aspect-ratio: 1;
    border-radius: var(--radius-xl); border: 2px solid transparent;
    background-color: var(--natural-surface); box-shadow: var(--shadow-md);
    padding: 24px; display: flex; flex-direction: column;
    justify-content: space-between; text-align: left;
    cursor: pointer; transition: all var(--transition-normal);
    overflow: hidden; font-family: var(--font-sans);
}
.uv-table-card:hover { box-shadow: var(--shadow-lg); transform: translateY(-4px); }
.uv-table-card:disabled { cursor: not-allowed; opacity: 0.7; }
.uv-table-selected { border-color: var(--natural-primary); box-shadow: 0 0 0 4px var(--natural-primary-light), var(--shadow-lg); }
.uv-table-used { border-color: rgba(239, 68, 68, 0.15); background-color: rgba(239, 68, 68, 0.02); }
.uv-table-opening { opacity: 0.7; }

.uv-table-header { display: flex; justify-content: space-between; align-items: flex-start; }
.uv-table-name { font-size: 1.8rem; font-weight: 300; font-style: italic; letter-spacing: -0.02em; color: var(--natural-primary); }
.uv-table-footer { position: relative; z-index: 2; }
.uv-table-status-used { display: flex; align-items: center; gap: 8px; color: #ef4444; font-family: var(--font-mono); font-size: 13px; font-weight: 700; }
.uv-pulse { animation: pulse-dot 2s infinite; }
.uv-table-status-free { display: flex; align-items: center; gap: 8px; color: #059669; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; }

.uv-table-overlay {
    position: absolute; inset: 0;
    background-color: rgba(74, 93, 78, 0.9);
    opacity: 0; display: flex; flex-direction: column;
    align-items: center; justify-content: center; gap: 8px;
    transition: opacity var(--transition-normal);
    border-radius: calc(var(--radius-xl) - 2px);
    color: white; z-index: 10;
}
.uv-table-overlay-view { background-color: rgba(37, 99, 235, 0.9); }
.uv-table-card:hover .uv-table-overlay { opacity: 1; }
.uv-overlay-icon { font-size: 2rem; }
.uv-overlay-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.2em; }

@media (max-width: 768px) {
    .uv-stats { grid-template-columns: 1fr; }
    .uv-filter-bar { flex-direction: column; align-items: flex-start; gap: 12px; }
    .uv-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>