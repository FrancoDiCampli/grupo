<template>
    <div>
        <v-row justify="center">
            <v-col>
                <v-sheet height="500">
                    <v-calendar
                        type="month"
                        :now="date"
                        :value="date"
                        :events="events"
                        :event-color="getEventColor"
                        :title="getEventTitle"
                    ></v-calendar>
                </v-sheet>
            </v-col>
        </v-row>
        <v-row>
            <v-col>
                <v-simple-table light dense dark fixed-header>
                    <thead>
                        <tr>
                            <th>
                                <b>{{date}}</b>
                            </th>
                            <th>Hoy</th>
                            <th>1 - 7 días</th>
                            <th>8 - 15 días</th>
                            <th>16 - 30 días</th>
                            <th>31 - 60 días</th>
                            <th>> - 61 días</th>
                            <th>Totales</th>
                        </tr>
                    </thead>
                    <tbody v-for="(item,index) in table" :key="index">
                        <tr>
                            <td>
                                <b>Cheques en $</b>
                            </td>
                            <td>${{item.hoy}}</td>
                            <td>${{item.siete}}</td>
                            <td>${{item.quince}}</td>
                            <td>${{item.treinta}}</td>
                            <td>${{item.sesenta}}</td>
                            <td>${{item.mas}}</td>
                            <td>${{item.total}}</td>
                        </tr>
                        <tr>
                            <td>
                                <b>Cantidad de cheques</b>
                            </td>
                            <td>{{item.hoyCount}}</td>
                            <td>{{item.sieteCount}}</td>
                            <td>{{item.quinceCount}}</td>
                            <td>{{item.treintaCount}}</td>
                            <td>{{item.sesentaCount}}</td>
                            <td>{{item.masCount}}</td>
                            <td>{{item.totalCount}}</td>
                        </tr>
                    </tbody>
                </v-simple-table>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default {
    data: () => ({
        events: [],
        table: [],
        date: new Date().toISOString().substr(0, 10)
    }),
    mounted() {
        axios
            .get("/api/cartera")
            .then(response => {
                this.events = response.data.calendar;
                this.table = response.data.table;
            })
            .catch(error => {
                console.log(error);
            });
    },
    methods: {
        getEventColor(event) {
            return event.color;
        },
        getEventTitle(event) {
            return event.title;
        }
    }
};
</script>

<style>
</style>