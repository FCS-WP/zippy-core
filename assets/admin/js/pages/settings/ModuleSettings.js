import { Box, Typography } from "@mui/material";
import React from "react";
import ModulesTable from "../../Components/Table/ModulesTable";

const ModuleSettings = () => {
  return (
    <Box>
      <Typography variant="h4" py={4}>Orders Configurations</Typography>
      <Box bgcolor={"white"} p={3}>
        <ModulesTable />
      </Box>
    </Box>
  );
};

export default ModuleSettings;
