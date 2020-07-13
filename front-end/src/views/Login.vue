<template>
<div>
  <div class="form-group">
    <h3 class="title" v-if="!isRegisterForm">Ingresa</h3>
    <h3 class="title" v-if="isRegisterForm">Registrate</h3>

    <div class="input-group" v-if="isRegisterForm">
      <label>Nombre</label>
      <input type="text" v-model="name">
    </div>
    <div class="input-group">
      <label>Documento</label>
      <input type="text" v-model="documento">
    </div>

    <div class="input-group" v-if="isRegisterForm">
      <label>Correo</label>
      <input type="email" v-model="email">
    </div>

    <div class="input-group" v-if="isRegisterForm">
      <label>Celular</label>
      <input type="number" v-model="celular">
    </div>

    <div class="input-group">
      <label>Contraseña</label>
      <input type="password" v-model="password">
    </div>

    <button type="button" @click="login" v-if="!isRegisterForm">Ingresa</button>
    <button type="button" @click="isRegisterForm = true" v-if="!isRegisterForm">Me quiero registrar</button>
    <button type="button" @click="registroCliente" v-if="isRegisterForm">Registrate</button>
    <button type="button" @click="isRegisterForm = false" v-if="isRegisterForm">Ya estoy registrado</button>
    </div>
  </div>
</template>

<script>
import { httpRequest, setAuthToken } from "@/helpers.js";

export default {
    name: 'Login',
    data() {
        return {
            name: '',
            documento: '',
            email: '',
            celular: '',
            password: '',
            isRegisterForm: false
        }
    },
    methods: {
        async login() {
            await httpRequest(this.$data, 'users/login')
              .then(res => {
                if(res.error) return alert(`${res.message} \n ${res.data}`);

                setAuthToken(res.data.data);
                this.$router.push('/');
              });
        },
        async registroCliente() {
            await httpRequest(this.$data, 'users')
              .then(res => {
                if(res.error) return alert(`${res.message} \n ${res.data}`);

                alert('Usuario registrado exitosamente');
                Object.assign(this.$data, this.$options.data());
              });
        },
    }
}
</script>

