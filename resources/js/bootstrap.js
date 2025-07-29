// axios
import axios from "axios";
window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// // jQuery
// import jQuery from "jquery";
// //window.jQuery = window.$ = $;
// window.jQuery = jQuery;

// Bootstrap
import * as bootstrap from "bootstrap";
window.bootstrap = bootstrap;

//sweetalert2
import swal from "sweetalert2";
window.Swal = swal;

// dayjs
import dayjs from "dayjs";
import "dayjs/locale/id";
dayjs.locale("id");
window.dayjs = dayjs;
