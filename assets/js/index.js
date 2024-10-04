import React from "react";
import ReactDOM from "react-dom/client";
import Dashboard from "./Components/Pages/Dashboad";
import Authentication from "./Components/Pages/Authentication";
import Settings from "./Components/Pages/Settings";

// Zippy Dashboard

document.addEventListener("DOMContentLoaded", function () {
  const zippyMain = document.getElementById("zippy-main");

  const zippyAuth = document.getElementById("zippy-authentication");

  if (typeof zippyAuth != "undefined" && zippyAuth != null) {
    const root = ReactDOM.createRoot(zippyAuth);
    root.render(<Authentication />);
  }

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
