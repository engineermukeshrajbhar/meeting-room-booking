<!-- resources/js/components/auth/Login.vue -->
<template>
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Login</div>
                <div class="card-body">
                    <form @submit.prevent="login">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" v-model="form.email" required>
                            <div class="text-danger" v-if="errors.email">{{ errors.email }}</div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" v-model="form.password" required>
                            <div class="text-danger" v-if="errors.password">{{ errors.password }}</div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" v-model="form.remember">
                            <label class="form-check-label" for="remember">Remember Me</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            form: {
                email: '',
                password: '',
                remember: false
            },
            errors: {}
        }
    },
    methods: {
        async login() {
            try {
                await this.$store.dispatch('login', this.form);
                this.$router.push('/');
            } catch (error) {
                if (error.response.status === 422) {
                    this.errors = error.response.data.errors;
                } else if (error.response.status === 401) {
                    this.errors = { email: error.response.data.message };
                }
            }
        }
    }
}
</script>