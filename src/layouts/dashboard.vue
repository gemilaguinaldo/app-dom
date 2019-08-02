<template>
    <q-layout id="dashboard-main-wrapper">
        <q-drawer
            v-model="drawer"
            content-class="bg-grey-9 q-pt-xl q-pb-xl"
            elevated
        >
            <div class="user-details q-mb-xl">
                <div class="avatar row items-center justify-center">
                    <q-icon name="mdi-account" size="120px" />
                </div>
                <h3 class="user-name q-mb-none text-white text-center"><strong>{{ user_name }}</strong></h3>
                <div class="user-role text-white text-center">{{ user_role }}</div>
            </div>
            <div class="menu-navigation">
                <div class="nav-item">
                    <router-link to="/dashboard/" class="link" exact>Dashboard</router-link>
                </div>
                <div class="nav-item">
                    <div class="link">User Management</div>
                    <div class="sub-navigation">
                        <template v-if="user_role == 'Administrator'">
                            <div class="sub-item">
                                <router-link to="/dashboard/user-management/roles" exact>Roles</router-link>
                            </div>
                            <div class="sub-item">
                                <router-link to="/dashboard/user-management/users" exact>Users</router-link>
                            </div>
                        </template>
                        <div class="sub-item">
                            <router-link to="/dashboard/user-management/change-password" exact>Change Password</router-link>
                        </div>
                    </div>
                </div>
                <div class="nav-item">
                    <div class="link">Expense Management</div>
                    <div class="sub-navigation">
                        <div class="sub-item" v-if="user_role == 'Administrator'">
                            <router-link to="/dashboard/expense-management/categories" exact>Expense Categories</router-link>
                        </div>
                        <div class="sub-item">
                            <router-link to="/dashboard/expense-management/expenses" exact>Expenses</router-link>
                        </div>
                    </div>
                </div>
            </div>
        </q-drawer>
        <q-page-container>
            <div class="top-bar q-pl-lg q-pr-lg q-pt-sm q-pb-sm bg-grey-3">
                <div class="row items-center justify-between">
                    <div class="breadcrumbs row items-center q-col-gutter-x-sm">
                        <div class="breadcrumb-item" v-if="this.$route.meta.parentMenu">{{ this.$route.meta.parentMenu }}</div>
                        <div class="breadcrumb-separator" v-if="this.$route.meta.parentMenu">
                            <q-icon name="mdi-chevron-right" size="24px" />
                        </div>
                        <div class="breadcrumb-item">{{ this.$route.meta.pageTitle }}</div>
                    </div>
                    <div>
                        <q-btn
                            label="Log Out"
                            icon="mdi-logout"
                            no-caps
                            unelevated
                            dense
                            flat
                            :ripple="false"
                            @click.prevent="logout()"
                        />
                    </div>
                </div>
            </div>
            <router-view :key="this.$route.name" />
        </q-page-container>
    </q-layout>
</template>
<script>
    export default {
        data() {
            return {
                drawer: true,
                user_role: store.getters[ 'users/details' ].role,
                user_name: store.getters[ 'users/details' ].name,
            }
        },
        methods: {
            logout() {
                store.dispatch( 'tokens/destroyToken' )
                .then( response => {
                    this.$router.push({
                        name: 'login'
                    })
                })
            }
        },
    }
</script>
<style lang="stylus" scoped>
    .user-details {
        .avatar {
            background-color: $white;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 10px;
        }
    }

    .menu-navigation {
        .nav-item {
            .link {
                color: $white;
                display: block;
                padding: 10px 20px;
                cursor: pointer;
                transition: .3s;

                &.active, &:hover {
                    background-color: $grey-8;
                }
            }

            .sub-navigation {
                .sub-item {
                    a {
                        color: $white;
                        display: block;
                        padding: 10px 20px 10px 40px;
                        cursor: pointer;
                        transition: .3s;

                        &.active, &:hover {
                            background-color: $grey-8;
                        }
                    }
                }
            }
        }
    }
</style>