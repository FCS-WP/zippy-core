import { Box, FormControl, FormGroup, Grid, MenuItem, Select, Typography } from "@mui/material";
import React from "react";

const PostalCode = ({ postalCodeChange, postalCode, ...props }) => {
  return (
    <div className="zippy-settings-content">
      <Box>
        <>
          <Grid container justifyContent={'center'} alignItems={'center'}>
            <Grid size={2} className="d-flex align-items-center">
              <Typography fontWeight={'bold'}>
                Enable
              </Typography>
            </Grid>
            <Grid size={4}>
              <FormControl sx={{ width: 300 }}>
                <Select
                  size="small"
                  value={postalCode}
                  id="enable_postalcode"
                  onChange={(e) => postalCodeChange(e)}
                  displayEmpty
                  inputProps={{ "aria-label": "Without label" }}
                >
                  <MenuItem value={1}>Yes</MenuItem>
                  <MenuItem value={0}>No</MenuItem>
                </Select>
              </FormControl>
            </Grid>
          </Grid>
        </>
      </Box>
    </div>
  );
};
export default PostalCode;
