<template>
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card my-5">
                    <form @submit.prevent="login" class="card-body cardbody-color p-lg-5">
                        <div class="text-center">
                            <img src="https://cdn.pixabay.com/photo/2016/03/31/19/56/avatar-1295397__340.png"
                                class="img-fluid profile-image-pic img-thumbnail rounded-circle my-3" width="200px"
                                alt="profile">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Email</label>
                            <input type="email" class="form-control" v-model="email" id="username"
                                aria-describedby="emailHelp" placeholder="Email">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" v-model="password" id="password"
                                placeholder="Password">
                        </div>
                        <div class="text-center"><button type="submit"
                                class="btn btn-color px-5 mb-5 w-100">Login</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>

import axios from 'axios';
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const email = ref('');
const password = ref('');
const token = ref(null);

const router = useRouter();

const login = async () => {
    try {
        axios.get('/sanctum/csrf-cookie').then()
        const response = await axios.post("/api/login", {
            email: email.value,
            password: password.value
        })

        if (response) {
            token.value = response.data.token
            localStorage.setItem('token', token.value);
            router.push({ name: 'dashboard' });
        } else {
            console.error(error);
            alert('Invalid credentials');
        }
    } catch (error) {

    }
}

</script>

<style scoped>
.btn-color {
    background-color: #0e1c36;
    color: #fff;
}

.btn-color:hover {
    background-color: #19325e;
}

.profile-image-pic {
    height: 200px;
    width: 200px;
    object-fit: cover;
}



.cardbody-color {
    background-color: #ebf2fa;
}

a {
    text-decoration: none;
}
</style>
