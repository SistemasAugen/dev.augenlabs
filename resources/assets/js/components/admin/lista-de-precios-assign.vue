<template>
    <div>
        {{ }}
    </div>
</template>

<script>
export default {
    data() {
        return {
            id: null, 
            priceList: null,
        }
    },
    methods: {
        getList() {
            this.$parent.inPetition = true;
            axios.get('https://apiv2.augenlabs.com/v1/lists/' + this.id).then(result => {
                console.log(result);
                this.priceList = result.data;
                this.$parent.inPetition = false;
            });
        },
    },
    mounted() {
        this.id = this.$route.params.id;
        this.getList();
    }
}
</script>

<style lang="scss" scoped>

</style>