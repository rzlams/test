<template>
  <div class="home">
    <img alt="Payco logo" src="../assets/payco.png" />
    <a href="#" @click="logout">Cerrar sesion</a>
<hr/>
    <h3 class="title">El saldo consultado es: {{ balance }}</h3>
    <div class="form-group">
      <h3 class="title">Consulta Saldo</h3>
      <div class="input-group">
        <label>Documento</label>
        <input type="text" v-model="documento">
      </div>
      <div class="input-group">
        <label>Celular</label>
        <input type="number" v-model="celular">
      </div>
      <button type="button" @click="consultaSaldo">Consultar</button>
    </div>
<hr/>
    <div class="form-group">
      <h3 class="title">Recargar Billetera</h3>
      <div class="input-group">
        <label>Documento</label>
        <input type="text" v-model="documento">
      </div>
      <div class="input-group">
        <label>Celular</label>
        <input type="number" v-model="celular">
      </div>
      <div class="input-group">
        <label>Monto</label>
        <input type="number" v-model="amount">
      </div>
      <button type="button" @click="recarga">Recargar Billetera</button>
    </div>
<hr/>
    <div class="form-group">
      <h3 class="title">Solicitar Pago a otro usuario</h3>
      <div>Ingresa un monto y luego selecciona un usuario</div>

      <div class="input-group">
        <label>Monto</label>
        <input type="number" v-model="amount">
      </div>

      <div class="input-group">
        <div v-if="registered_users.length == 0">
          No hay usuarios registrados
        </div>

        <div
          class="input-group"
          @click="pago($event, ru)"
          v-if="registered_users.length > 0"
          v-for="ru in registered_users"
        >
          Usuario N° {{ ru }}
        </div>
      </div>
    </div>
<hr/>
    <div class="form-group">
      <h3 class="title">Aprobar solicitud de pago</h3>

      <div v-if="pending_transactions.length == 0">
        No hay transacciones por aprobar
      </div>

      <div
        class="input-group"
        @click="correoConfirmacion($event, pt)"
        v-if="pending_transactions.length > 0"
        v-for="pt in pending_transactions"
      >
        Transaccion N° {{ pt }}
      </div>
    </div>

    <div class="form-group">
      <h3 class="title">Confirmar Pago</h3>
      <div class="input-group">
        <label>Codigo Validacion</label>
        <input type="number" v-model="confirmation_token">
      </div>
      <button type="button" @click="confirmaPago">Aceptar</button>
      <br/>
      <button type="button" @click="correoConfirmacion($event, transaction_id)">
        Reenviar codigo de autorizacion
      </button>
    </div>
  </div>
</template>

<script>
import { httpRequest, getAuthToken, clearAuthToken } from "@/helpers.js";

export default {
  name: "Home",
  data() {
        return {
            documento: '',
            celular: '',
            amount: 0,
            sender_id: '',
            confirmation_token: '',
            transaction_id: '',
            session_token: '',
            pending_transactions: [],
            registered_users: [],
            balance: 0,
        }
    },
    mounted() {
      this.listarTransaccionesPendientes(),
      this.listarUsuarios()
    },
    methods: {
        async listarTransaccionesPendientes() {
          this.session_token = getAuthToken();
          await httpRequest(this.$data, 'listar-pendientes')
            .then(res => {
              if(res.error) return alert(`${res.message} \n ${res.data}`);
              if(res.data.data == 0) return;
              this.pending_transactions = res.data.data.split('|&|');
            });
        },
        async listarUsuarios() {
          this.session_token = getAuthToken();
          await httpRequest(this.$data, 'listar-usuarios')
            .then(res => {
              if(res.error) return alert(`${res.message} \n ${res.data}`);
              if(res.data.data == 0) return;
              this.registered_users = res.data.data.split('|&|');
            });
        },
        async consultaSaldo() {
          await httpRequest(this.$data, 'users/saldo')
            .then(res => {
              if(res.error) return alert(`${res.message} \n ${res.data}`);
              this.balance = res.data.data;
            });
        },
        async logout() {
          await httpRequest(this.$data, 'users/logout')
            .then(res => {
              if(res.error) return alert(`${res.message} \n ${res.data}`);

              clearAuthToken();
              this.$router.push('/login');
            });
        },
        async recarga() {
          await httpRequest(this.$data, 'recarga')
            .then(res => {
              if(res.error) return alert(`${res.message} \n ${res.data}`);
              alert('Recarga exitosa');
              this.consultaSaldo();
            });
        },
        async pago($event, sender_id) {
          this.sender_id = sender_id;
          await httpRequest(this.$data, 'pago')
            .then(res => {
              if(res.error) return alert(`${res.message} \n ${res.data}`);
              alert('Solicitud enviada');
            });
        },
        async correoConfirmacion($event, transaction_id) {
          this.transaction_id = transaction_id;
          await httpRequest(this.$data, 'correo-confirmacion')
            .then(res => {
              if(res.error) return alert(`${res.message} \n ${res.data}`);
              alert('Enviamos un codigo de autorizacion a su correo');
            });
        },
        async confirmaPago() {
          await httpRequest(this.$data, 'confirma-pago')
            .then(res => {
              if(res.error) return alert(`${res.message} \n ${res.data}`);
              alert('Pago aprobado');
              this.listarTransaccionesPendientes();
            });
        }
    }
};
</script>
