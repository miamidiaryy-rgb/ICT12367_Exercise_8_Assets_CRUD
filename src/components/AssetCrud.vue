<script setup>
import { ref, onMounted } from 'vue'
import { API_BASE_URL } from '../config'

const assets = ref([])
const loading = ref(false)
const mode = ref('list') // 'list' | 'add' | 'edit'
const statusMsg = ref('')
const errorMsg = ref('')
const deleteTarget = ref(null)
const selectedFile = ref(null)

const form = ref({
  asset_id: null,
  asset_code: '',
  asset_name: '',
  purchase_date: '',
  price: '',
  image: '',
})

const imageUrl = (filename) => {
  if (!filename) return ''
  return `${API_BASE_URL}/uploads/${filename}`
}

const formatPrice = (val) => {
  return Number(val || 0).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })
}

const fetchAssets = async () => {
  loading.value = true
  errorMsg.value = ''
  try {
    const res = await fetch(`${API_BASE_URL}/show_assets.php`)
    const result = await res.json()
    if (result.success) {
      assets.value = result.data
    } else {
      errorMsg.value = result.message || 'ไม่สามารถโหลดข้อมูลอุปกรณ์ได้'
    }
  } catch (err) {
    errorMsg.value = 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ PHP API ได้'
  } finally {
    loading.value = false
  }
}

const openAddForm = () => {
  statusMsg.value = ''
  errorMsg.value = ''
  selectedFile.value = null
  form.value = {
    asset_id: null,
    asset_code: '',
    asset_name: '',
    purchase_date: '',
    price: '',
    image: '',
  }
  mode.value = 'add'
}

const openEditForm = (item) => {
  statusMsg.value = ''
  errorMsg.value = ''
  selectedFile.value = null
  form.value = {
    asset_id: item.asset_id,
    asset_code: item.asset_code,
    asset_name: item.asset_name,
    purchase_date: item.purchase_date || '',
    price: item.price,
    image: item.image || '',
  }
  mode.value = 'edit'
}

const onFileChange = (e) => {
  const file = e.target.files?.[0] || null
  selectedFile.value = file
  if (file) {
    form.value.image = file.name
  }
}

const handleSubmit = async () => {
  statusMsg.value = ''
  errorMsg.value = ''
  const isEdit = mode.value === 'edit'
  const endpoint = isEdit ? 'update_asset.php' : 'add_asset.php'

  try {
    let res
    if (selectedFile.value) {
      const fd = new FormData()
      if (isEdit) fd.append('asset_id', form.value.asset_id)
      fd.append('asset_code', form.value.asset_code)
      fd.append('asset_name', form.value.asset_name)
      fd.append('purchase_date', form.value.purchase_date)
      fd.append('price', form.value.price)
      fd.append('image', form.value.image)
      fd.append('image_file', selectedFile.value)
      res = await fetch(`${API_BASE_URL}/${endpoint}`, { method: 'POST', body: fd })
    } else {
      res = await fetch(`${API_BASE_URL}/${endpoint}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          ...form.value,
          price: Number(form.value.price || 0),
        }),
      })
    }

    const result = await res.json()
    if (result.success) {
      statusMsg.value = isEdit
        ? `แก้ไขข้อมูลอุปกรณ์ ${form.value.asset_code} (${form.value.asset_name}) เรียบร้อยแล้ว`
        : `เพิ่มข้อมูลอุปกรณ์ ${form.value.asset_code} (${form.value.asset_name}) เรียบร้อยแล้ว`
      mode.value = 'list'
      await fetchAssets()
    } else {
      errorMsg.value = result.message || 'บันทึกข้อมูลไม่สำเร็จ'
    }
  } catch (err) {
    errorMsg.value = 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ PHP API ได้'
  }
}

const confirmDelete = async () => {
  if (!deleteTarget.value) return
  const target = deleteTarget.value
  statusMsg.value = ''
  errorMsg.value = ''
  try {
    const res = await fetch(`${API_BASE_URL}/delete_asset.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ asset_id: target.asset_id }),
    })
    const result = await res.json()
    if (result.success) {
      statusMsg.value = `ลบข้อมูลอุปกรณ์ ${target.asset_code} (${target.asset_name}) เรียบร้อยแล้ว`
      deleteTarget.value = null
      await fetchAssets()
    } else {
      errorMsg.value = result.message || 'ลบข้อมูลไม่สำเร็จ'
    }
  } catch (err) {
    errorMsg.value = 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ PHP API ได้'
  }
}

onMounted(fetchAssets)
</script>

<template>
  <div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">จัดการข้อมูลอุปกรณ์ (Assets CRUD)</h4>
      <button v-if="mode === 'list'" class="btn btn-primary btn-sm" @click="openAddForm">
        + เพิ่มข้อมูลอุปกรณ์
      </button>
      <button v-else class="btn btn-secondary btn-sm" @click="mode = 'list'; errorMsg = ''">
        กลับไปหน้าแสดงข้อมูล
      </button>
    </div>

    <div v-if="statusMsg" class="alert alert-success py-2">{{ statusMsg }}</div>
    <div v-if="errorMsg" class="alert alert-danger py-2">{{ errorMsg }}</div>

    <!-- ฟอร์มเพิ่ม / แก้ไขข้อมูลอุปกรณ์ -->
    <div v-if="mode === 'add' || mode === 'edit'" class="card mx-auto" style="max-width: 650px">
      <div class="card-header bg-light">
        <h5 class="mb-0">
          {{ mode === 'edit' ? `แก้ไขข้อมูลอุปกรณ์ (${form.asset_code})` : 'เพิ่มข้อมูลอุปกรณ์' }}
        </h5>
      </div>
      <div class="card-body">
        <form @submit.prevent="handleSubmit">
          <div class="row mb-3">
            <div class="col-md-4">
              <label class="form-label">รหัสครุภัณฑ์ <span class="text-danger">*</span></label>
              <input
                v-model="form.asset_code"
                type="text"
                class="form-control"
                placeholder="เช่น IT006"
                required
              />
            </div>
            <div class="col-md-8">
              <label class="form-label">ชื่อครุภัณฑ์ <span class="text-danger">*</span></label>
              <input
                v-model="form.asset_name"
                type="text"
                class="form-control"
                placeholder="เช่น จอมอนิเตอร์ 27 นิ้ว"
                required
              />
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">วันที่ซื้อ</label>
              <input v-model="form.purchase_date" type="date" class="form-control" />
            </div>
            <div class="col-md-6">
              <label class="form-label">ราคา (บาท)</label>
              <input
                v-model="form.price"
                type="number"
                step="0.01"
                min="0"
                class="form-control"
                placeholder="เช่น 6500.00"
              />
            </div>
          </div>

          <div class="row mb-4">
            <div class="col-md-6">
              <label class="form-label">ชื่อไฟล์รูปภาพ</label>
              <input
                v-model="form.image"
                type="text"
                class="form-control"
                placeholder="เช่น monitor.jpg"
              />
            </div>
            <div class="col-md-6">
              <label class="form-label">หรืออัปโหลดรูปภาพ</label>
              <input type="file" accept="image/*" class="form-control" @change="onFileChange" />
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" @click="mode = 'list'">ยกเลิก</button>
            <button type="submit" class="btn btn-primary">
              {{ mode === 'edit' ? 'บันทึกการแก้ไข' : 'บันทึกข้อมูล' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ตารางแสดงข้อมูลอุปกรณ์ -->
    <div v-else>
      <div v-if="loading" class="text-muted py-3">กำลังโหลดข้อมูล...</div>
      <div v-else class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
          <thead class="table-dark">
            <tr>
              <th class="text-center" style="width: 60px">ลำดับ</th>
              <th class="text-center" style="width: 95px">รูปภาพ</th>
              <th>รหัสครุภัณฑ์</th>
              <th>ชื่อครุภัณฑ์</th>
              <th>วันที่ซื้อ</th>
              <th class="text-end">ราคา (บาท)</th>
              <th>ไฟล์รูปภาพ</th>
              <th class="text-center" style="width: 135px">จัดการ</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in assets" :key="item.asset_id">
              <td class="text-center">{{ item.asset_id }}</td>
              <td class="text-center">
                <img
                  v-if="item.image"
                  :src="imageUrl(item.image)"
                  :alt="item.asset_name"
                  class="img-thumbnail"
                  style="width: 64px; height: 46px; object-fit: cover"
                />
                <span v-else class="text-muted small">-</span>
              </td>
              <td>{{ item.asset_code }}</td>
              <td>{{ item.asset_name }}</td>
              <td>{{ item.purchase_date }}</td>
              <td class="text-end">{{ formatPrice(item.price) }}</td>
              <td class="small text-muted">{{ item.image }}</td>
              <td class="text-center">
                <button class="btn btn-warning btn-sm me-1" @click="openEditForm(item)">แก้ไข</button>
                <button class="btn btn-danger btn-sm" @click="deleteTarget = item">ลบ</button>
              </td>
            </tr>
            <tr v-if="assets.length === 0">
              <td colspan="8" class="text-center text-muted py-3">ไม่มีข้อมูลอุปกรณ์</td>
            </tr>
          </tbody>
        </table>
      </div>
      <p class="text-muted small mb-0">จำนวนอุปกรณ์ทั้งหมด {{ assets.length }} รายการ</p>
    </div>

    <!-- กล่องยืนยันการลบ -->
    <div v-if="deleteTarget" class="modal d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.4)">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">ยืนยันการลบข้อมูลอุปกรณ์</h5>
            <button type="button" class="btn-close" @click="deleteTarget = null"></button>
          </div>
          <div class="modal-body">
            ต้องการลบข้อมูลอุปกรณ์ <strong>{{ deleteTarget.asset_code }} - {{ deleteTarget.asset_name }}</strong> ใช่หรือไม่?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" @click="deleteTarget = null">ยกเลิก</button>
            <button type="button" class="btn btn-danger btn-sm" @click="confirmDelete">ยืนยันการลบ</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
