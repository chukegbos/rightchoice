require('./bootstrap');

window.Vue = require('vue');

import moment from 'moment';
import { Form, HasError, AlertError, AlertSuccess } from 'vform';
import VueProgressBar from 'vue-progressbar';
import JsonCSV from 'vue-json-csv';

Vue.component('downloadCsv', JsonCSV);

Vue.use(VueProgressBar, {
	color: 'rgb(143, 255, 199)',
	failedColor: 'red',
	height: '4px'
});

window.Form = Form;

Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);
Vue.component(AlertSuccess.name, AlertSuccess)

Vue.component('pagination', require('laravel-vue-pagination'));
import VueRouter from 'vue-router';
Vue.use(VueRouter);

import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';

// Install BootstrapVue
Vue.use(BootstrapVue);
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin);

import Swal from 'sweetalert2';
window.Swal = Swal;

const Toast = Swal.mixin({
	toast: true,
	position: 'top-end',
	showConfirmButton: false,
	timer: 7000,
	timerProgressBar: true,
	onOpen: (toast) => {
		toast.addEventListener('mouseenter', Swal.stopTimer);
		toast.addEventListener('mouseleave', Swal.resumeTimer);
	}
});

window.Toast = Toast;

let Fuse = new Vue();
window.Fuse = Fuse;

import CKEditor from 'ckeditor4-vue';
Vue.use(CKEditor);

import Select2 from 'v-select2-component';
Vue.component('Select2', Select2);

import VueHtmlToPaper from 'vue-html-to-paper';

const options = {
  name: '_blank',
  specs: [
    'fullscreen=yes',
    'titlebar=yes',
    'scrollbars=yes'
  ],
  styles: [
    'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css',
    'https://unpkg.com/kidlat-css/css/kidlat.css'
  ]
}

Vue.use(VueHtmlToPaper, options);

import DashboardComponent from './components/DashboardComponent.vue';
import StoreComponent from './components/StoreComponent.vue';
import CashierComponent from './components/CashierComponent.vue';
import ManagerComponent from './components/ManagerComponent.vue';
import CustomerComponent from './components/CustomerComponent.vue';
import SuppliersComponent from './components/SuppliersComponent.vue';
import AdminComponent from './components/AdminComponent.vue';
import InventoryComponent from './components/InventoryComponent.vue';
import InventoryCategoryComponent from './components/InventoryCategoryComponent.vue';
import SettingComponent from './components/SettingComponent.vue';
import StoreInventoryComponent from './components/StoreInventoryComponent.vue';

import StoreTradeComponent from './components/StoreTradeComponent.vue';
import ShowRoomComponent from './components/ShowRoomComponent.vue';
import TargetComponent from './components/TargetComponent.vue';
import StoreRequestComponent from './components/StoreRequestComponent.vue';
import CreateRequestComponent from './components/CreateRequestComponent.vue';
import CreateMoveComponent from './components/CreateMoveComponent.vue';
import MoveBackComponent from './components/MoveBackComponent.vue';
import ViewRequestComponent from './components/ViewRequestComponent.vue';
import PurchasesComponent from './components/PurchasesComponent.vue';
import PurchaseFormComponent from './components/PurchaseFormComponent.vue';
import ViewPurchaseComponent from './components/ViewPurchaseComponent.vue';
import DischargeComponent from './components/DischargeComponent.vue';
import ShoppingCartComponent from './components/ShoppingCartComponent.vue';
import OrderReportComponent from './components/OrderReportComponent.vue';
import OrderDebtorComponent from './components/OrderDebtorComponent.vue';
import DebtorViewComponent from './components/DebtorViewComponent.vue';
import ProfileComponent from './components/ProfileComponent.vue';
import IncomeCategoryComponent from './components/IncomeCategoryComponent.vue';
import IncomeComponent from './components/IncomeComponent.vue';
import ExpenditureCategoryComponent from './components/ExpenditureCategoryComponent.vue';
import ExpenditureComponent from './components/ExpenditureComponent.vue';
import BalanceComponent from './components/BalanceComponent.vue';
import OrderViewComponent from './components/OrderViewComponent.vue';

let routes = [
    {
        path: '/home',
        component: DashboardComponent
    },

    {
        path: '/admin/stores',
        component: StoreComponent
    },

    {
        path: '/admin/cashiers',
        component: CashierComponent
    },

    {
        path: '/admin/customers',
        component: CustomerComponent
    },

    {
        path: '/admin/suppliers',
        component: SuppliersComponent
    },

    {
        path: '/admin/store-manager',
        component: ManagerComponent
    },

    {
        path: '/admin/admins',
        component: AdminComponent
    },

    {
        path: '/admin/inventory',
        component: InventoryComponent
    },

    {
        path: '/admin/inventory/category',
        component: InventoryCategoryComponent
    },

    {
        path: '/admin/inventory/:id',
        component: InventoryComponent
    },
    
    {
        path: '/admin/setting',
        component: SettingComponent
    },

    {
        path: '/admin/purchases',
        component: PurchasesComponent
    },

    {
        path: '/admin/purchase/create/:purchase_id',
        component: PurchaseFormComponent
    },

    {
        path: '/admin/purchases/:id',
        component: ViewPurchaseComponent
    },
    {
        path: '/admin/user/:id',
        component: ProfileComponent
    },

    {
        path: '/store/inventory',
        component: StoreInventoryComponent
    },

    {
        path: '/store/trade',
        component: StoreTradeComponent
    },

    {
        path: '/store/request',
        component: StoreRequestComponent
    },

    {
        path: '/store/request/create',
        component: CreateRequestComponent
    },

    {
        path: '/store/request/create/:req_id',
        component: CreateRequestComponent
    },

    {
        path: '/store/move/create/:ref_id',
        component: CreateMoveComponent
    },

    {
        path: '/store/moveback/create/:ref_id',
        component: MoveBackComponent
    },

    {
        path: '/orderview/:sale_id',
        component: OrderViewComponent
    },

    {
        path: '/store/request/view/:req_id',
        component: ViewRequestComponent
    },

    {
        path: '/admin/discharge',
        component: DischargeComponent
    },

    {
        path: '/admin/report',
        component: OrderReportComponent
    },

    {
        path: '/store/debtors',
        component: OrderDebtorComponent
    },

    {
        path: '/store/debtors/:sale_id',
        component: DebtorViewComponent
    },

    {
        path: '/store/inventory/:store_code',
        component: StoreInventoryComponent
    },

    {
        path: '/store/trade/:store_code',
        component: StoreTradeComponent
    },

    {
        path: '/store/room/:store_code',
        component: ShowRoomComponent
    },

    {
        path: '/store/target/:store_code',
        component: TargetComponent
    },

    {
        path: '/store/report/:code',
        component: OrderReportComponent
    },

    {
        path: '/sale/shopping-cart',
        component: ShoppingCartComponent
    },   

    {
        path: '/admin/account/income',
        component: IncomeComponent
    },

    {
        path: '/admin/account/income/category',
        component: IncomeCategoryComponent
    },

    {
        path: '/admin/account/expenditure',
        component: ExpenditureComponent
    },

    {
        path: '/admin/account/expenditure/category',
        component: ExpenditureCategoryComponent
    },

    {
        path: '/admin/account/balance',
        component: BalanceComponent
    },
];

const router = new VueRouter({
	mode: 'history',
	routes // short for `routes: routes`
});

Vue.filter('capitalize', function(value) {
	if (!value) return '';
	value = value.toString();
	return value.charAt(0).toUpperCase() + value.slice(1);
});

Vue.filter('myDate', function(created) {
	if (!created) return '';
	return moment(created).format('MMMM Do YYYY');
});

Vue.filter('toCurrency', function(value) {
	if (typeof value !== 'number') {
		return value;
	}
	var formatter = new Intl.NumberFormat('en-US', {
		style: 'currency',
		currency: 'USD',
		minimumFractionDigits: 0
	});
	return formatter.format(value);
});

const app = new Vue({
	el: '#app',
	router,
	data: {
		search: ''
	},
	methods: {
		searchit() {
			Fuse.$emit('searching');
		}
	}
});