import { Box, Tab, Tabs } from "@mui/material";
import React from "react";
import InvoiceSettings from "./InvoiceSettings";

const CustomTabPanel = (props) => {
  const { children, value, index, ...other } = props;

  return (
    <div
      role="tabpanel"
      hidden={value !== index}
      id={`simple-tabpanel-${index}`}
      aria-labelledby={`simple-tab-${index}`}
      {...other}
    >
      {value === index && <Box p={2}>{children}</Box>}
    </div>
  );
};

const SettingOrderTabs = () => {
  const [value, setValue] = React.useState(0);

  const handleChange = (event, newValue) => {
    setValue(newValue);
  };

  return (
    <Box sx={{ width: "100%", typography: "body1" }}>
      <Tabs
        value={value}
        onChange={handleChange}
        aria-label="basic tabs example"
      >
        <Tab label="Common" />
        <Tab label="Invoices" />
      </Tabs>
      <Box>
        <CustomTabPanel value={value} index={0}>
          Common content of Orders
        </CustomTabPanel>
        <CustomTabPanel value={value} index={1}>
          <InvoiceSettings />
        </CustomTabPanel>
      </Box>
    </Box>
  );
};

export default SettingOrderTabs;
