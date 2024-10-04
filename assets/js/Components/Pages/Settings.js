import React from "react";
import { Tab, Tabs } from "react-bootstrap";
import { useState } from "react";
import Auth from "./Auth/Auth";
const Settings = () => {
  const [key, setKey] = useState("dashboad");
  return (
    <Tabs
      id="controlled-tab-example"
      activeKey={key}
      onSelect={(k) => setKey(k)}
    >
      <Tab eventKey="dashboad" title="Analytics Woocommerce">
        <Auth />
      </Tab>
      <Tab eventKey="postal_code" title="Postal Code">
        shin 2
      </Tab>
      <Tab eventKey="mail" title="Mail Setting">
        shin 3
      </Tab>
    </Tabs>
  );
};
export default Settings;
