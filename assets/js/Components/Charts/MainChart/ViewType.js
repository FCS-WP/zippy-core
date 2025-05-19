import { Badge, Box, Grid, Menu, MenuItem, Select } from "@mui/material";
import React, { useEffect } from "react";

const ViewType = ({
  viewTypeSelected,
  onClickViewType,
  currentViewBy,
  onClearDate,
}) => {
  const viewTypes = [
    {
      title: "By Day",
      key: "day",
    },
    {
      title: "By Week",
      key: "week",
    },
    {
      title: "By Month",
      key: "month",
    },
  ];

  return (
    <Box mb={3}>
      <Grid container>
        <Grid size={6} className="d-flex view-type">
          <span> Currently viewing By:</span>
          {currentViewBy?.name && (
            <div className="font-weight-bold small">
              <Badge
                variant="dark"
                className="badge-dark inline-block align-items-center ml-2 pl-2 pr-2 d-flex"
                onClick={() => onClearDate(currentViewBy.type)}
              >
                <p className="select-bage"> {currentViewBy.name}</p>
                <button className="close-btn" aria-label="Clear selected date">
                  ×
                </button>
              </Badge>
            </div>
          )}
        </Grid>
        <Grid size={6}>
          <Select fullWidth size="small" value={viewTypeSelected}>
            {viewTypes.map((viewType) => (
              <MenuItem
                className="btn-viewby"
                onClick={() => onClickViewType(viewType.key)}
                value={viewType.key}
                key={viewType.key}
              >
                {viewType.title}
              </MenuItem>
            ))}
          </Select>
        </Grid>
      </Grid>
    </Box>
  );
};
export default ViewType;
