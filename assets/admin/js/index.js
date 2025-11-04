import React from "react";
import ReactDOM from "react-dom/client";
import Dashboard from "./pages/dashboard/Dashboad";
import Settings from "./pages/setting/Settings";
import { ThemeProvider } from "react-bootstrap";
import OrdersPage from "./pages/orders/OrdersPage";
import ModuleSettings from "./pages/setting/ModuleSettings";
import TableOrderInfo from "./Components/Pages/Orders/order-info/TableOrderInfo";

// Zippy Dashboard

document.addEventListener("DOMContentLoaded", function () {
  const zippyMain = document.getElementById("zippy-main");

  if (typeof zippyMain != "undefined" && zippyMain != null) {
    const root = ReactDOM.createRoot(zippyMain);
    root.render(<Dashboard />);
  }
});

// Zippy Settings

document.addEventListener("DOMContentLoaded", function () {
  const zippySetting = document.getElementById("zippy-settings");

  if (typeof zippySetting != "undefined" && zippySetting != null) {
    const root = ReactDOM.createRoot(zippySetting);
    root.render(<Settings />);
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const zippyOrdersPage = document.getElementById("orders-page");

  if (zippyOrdersPage) {
    const root = ReactDOM.createRoot(zippyOrdersPage);
    root.render(<OrdersPage />);
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const adminOrderTable = document.getElementById("admin-table-order");

  if (adminOrderTable) {
    const root = ReactDOM.createRoot(adminOrderTable);
    const orderId = adminOrderTable.getAttribute("data-order-id");
    const enableEdit = adminOrderTable.getAttribute("data-enable-edit");
    root.render(<TableOrderInfo orderId={orderId} enableEdit={enableEdit} />);
  }
});

// Modules Control

document.addEventListener("DOMContentLoaded", function () {
  const moduelsControl = document.getElementById("core_settings");

  if (moduelsControl) {
    const root = ReactDOM.createRoot(moduelsControl);
    root.render(<ModuleSettings />);
  }
});
