<template>
    <q-layout id="auth-main-wrapper">
        <q-page-container>
            <router-view :key="this.$route.name" />
        </q-page-container>
    </q-layout>
</template>
<script>
    export default {

    }
</script>
<style lang="stylus" scoped>

</style>