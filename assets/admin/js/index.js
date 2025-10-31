import React from "react";
import ReactDOM from "react-dom/client";
import Dashboard from "./pages/dashboard/Dashboad";
import Settings from "./pages/setting/Settings";
import { ThemeProvider } from "react-bootstrap";
import Orders from "./pages/orders/Orders";

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
    root.render(<Orders />);
  }
});
