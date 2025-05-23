import React, { useEffect, useCallback } from "react";
import { useState } from "react";
import Authentication from "./Auth/Authentication";
import PostalCode from "./PostalCode";
import { Api } from "../../api";
import CustomizeShipping from "./Shipping/CustomizeShipping";
import { Box, Button, Tab, Tabs } from "@mui/material";
import CustomTabPanel from "../Layouts/CustomTabPanel";
import { toast, ToastContainer } from "react-toastify";

const Settings = () => {
  const [tabValue, setTabValue] = React.useState(0);
  const [postalCode, setPostalCode] = useState(0);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [shipData, setShipData] = useState();

  const updateTempShippingData = (newData) => {
    setShipData(newData);
  }

  const handlePostcodeChange = (e) => {
    setPostalCode(e.target.value);
  };

  const handleChangeTabValue = (event, newValue) => {
    setTabValue(newValue);
  };

  const handleSubmit = async (e) => {
    switch (tabValue) {
      case 1:
        const params = {
          key: "_zippy_postal_code",
          value: postalCode,
        };
        setLoading(true);
        const { data } = await Api.updateSettings(params);
        window.location.reload();
        break;
      case 2:
        setLoading(true);
        const saveResponse = await Api.saveShippingConfigs({
          min_cost: shipData.cost,
          is_active: shipData.active
        });

        if (!saveResponse || saveResponse.data.success !== true) {
          toast.error("Error when update shipping config!");
          return;
        } else {
          window.location.reload();
        }
      default:
        break;
    }
  };

  const fetchData = useCallback(async (params) => {
    try {
      const { data } = await Api.checkKeyExits(params);
      if (data.message === "unauthorized") {
        setPostalCode(0);
      } else {
        setPostalCode(1);
      }
    } catch (err) {
      setError("Failed to fetch authentication status");
      console.error(err);
    } finally {
      // setLoading(false);
    }
  }, []);

  useEffect(() => {
    const params = { key: "_zippy_postal_code" };
    fetchData(params);
  }, [fetchData, loading]);

  function a11yProps(index) {
    return {
      id: `simple-tab-${index}`,
      "aria-controls": `simple-tabpanel-${index}`,
    };
  }

  return (
    <Box>
      <Box>
        <Box>
          <Tabs
            value={tabValue}
            onChange={handleChangeTabValue}
            aria-label="basic tabs example"
            sx={{ padding: 0 }}
          >
            <Tab label="Analytics Woocommerce" {...a11yProps(0)} />
            <Tab label="Postal Code" {...a11yProps(1)} />
            <Tab label="Shipping Fees" {...a11yProps(2)} />
          </Tabs>
        </Box>

        <CustomTabPanel value={tabValue} index={0}>
          <Authentication />
        </CustomTabPanel>
        <CustomTabPanel value={tabValue} index={1}>
          <PostalCode
            postalCodeChange={handlePostcodeChange}
            postalCode={postalCode}
          />
        </CustomTabPanel>
        <CustomTabPanel value={tabValue} index={2}>
          <CustomizeShipping onUpdateData={updateTempShippingData} />
        </CustomTabPanel>
      </Box>
      <Button sx={{ mt: 3 }} onClick={handleSubmit} variant="contained">
        Save Changes
      </Button>
      <ToastContainer />
    </Box>
  );
};
export default Settings;
