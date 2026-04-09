// resources/js/app.js

// Global utilities
import "./global/global-datepicker.js";

// Employee scripts
import "./employee/employee-form.js";

// HR scripts
import "./HR/leave-management.js";
import "./HR/overtime-management.js";
import "./HR/attendance-table.js";
import "./HR/payroll-batch-edit-adapter.js";

// Payroll scripts
//  import "./Payroll/create-payroll.js";
//  import "./Payroll/edit-payroll.js";
import "./Payroll/payroll-form.js";

// Template scripts (init / dashboard / widgets)
//
// The original Bootstrap 5 template bundled many "init" files that assume a
// large set of jQuery plugins are globally available (daterangepicker,
// DataTables, Quill, etc.). In this Laravel integration, many of those plugins
// have been removed, so importing these init files globally causes large
// amounts of console errors and can break unrelated pages.
//
// If a specific page still needs a template init script, load it from that page
// only (via Blade @push('scripts') or a targeted import).

// Optional: confirm app.js loaded
console.log("App.js loaded via Vite");
