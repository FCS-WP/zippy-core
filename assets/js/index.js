import React from "react";
import ReactDOM from "react-dom/client";
import Dashboard from "./Components/Layouts/Dashboad";

document.addEventListener("DOMContentLoaded", function () {
  const zippyRoot = document.getElementById("zippy-root");

  if (typeof zippyRoot != "undefined" && zippyRoot != null) {
    const root = ReactDOM.createRoot(zippyRoot);
    root.render(<Dashboard />);
  }
});
