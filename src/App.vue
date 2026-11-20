<script setup>
import AssetCrud from './components/AssetCrud.vue'
</script>

<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container">
      <a class="navbar-brand fw-semibold" href="#">ระบบจัดการข้อมูลอุปกรณ์</a>
      <div class="navbar-nav ms-auto">
        <a class="nav-link active fw-semibold" aria-current="page" href="#">รายการอุปกรณ์</a>
      </div>
    </div>
  </nav>

  <AssetCrud />
</template>

<style>
body {
  font-family: 'Leelawadee UI', Tahoma, sans-serif;
  background-color: #ffffff;
}
</style>
