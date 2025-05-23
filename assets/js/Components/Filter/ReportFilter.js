import React, { useState } from "react";
import DatePicker from "react-datepicker";
import { DateHelper } from "../../helper/date-helper";
import { Box, Button, Grid } from "@mui/material";

const ReportFilter = ({
  onClick,
  activeFilter,
  handleCustomDate,
  ...props
}) => {
  const filters = [
    {
      title: "Year",
      key: "year",
    },
    {
      title: "Last month",
      key: "last_month",
    },
    {
      title: "This Month",
      key: "this_month",
    },
    {
      title: "Last 7 days",
      key: "last_week",
    },
    {
      title: "Custom",
      key: "custom",
    },
  ];

  const handleFilterDate = (key) => {
    onClick(key);
  };

  const [startDate, setStartDate] = useState(DateHelper.getDayStartMonth());
  const [endDate, setEndDate] = useState(DateHelper.getDayEndMonth());

  const handleSelectedStartdDay = (date) => {
    setStartDate(date);
  };
  const handleSelectedEndDay = (date) => {
    setEndDate(date);
  };

  const handleSubmit = () => {
    const dataParams = {
      date_start: startDate,
      date_end: endDate,
    };
    handleCustomDate(dataParams);
  };
  return (
    <Grid container>
      <Grid>
        <Box display={"flex"} gap={2}>
          {filters.map((filter) => (
            <Button
              key={filter.key}
              onClick={() => handleFilterDate(filter.key)}
              variant={
                activeFilter === filter.key ? "contained" : "outlined"
              }
            >
              {filter.title}
            </Button>
          ))}
          {activeFilter === "custom" && (
            <Box
              display={"flex"}
              alignItems={"center"}
              className="date-picker"
            >
              <DatePicker
                className="ml-3 mr-1"
                selected={startDate}
                onChange={(date) => handleSelectedStartdDay(date)}
                dateFormat="yyyy/MM/dd"
                selectsStart
                startDate={startDate}
                endDate={endDate}
                maxDate={endDate}
              />
              <span className="font-bold"> ~ </span>
              <DatePicker
                className={
                  activeFilter === "custom" ? "ml-1 active" : "ml-1"
                }
                onChange={(date) => handleSelectedEndDay(date)}
                dateFormat="yyyy/MM/dd"
                selectsEnd
                selected={endDate}
                startDate={startDate}
                endDate={endDate}
                minDate={startDate}
                maxDate={new Date()}
              />
              <Button
                sx={{ ml: 1 }}
                variant="contained"
                color="success"
                onClick={handleSubmit}
              >
                Go
              </Button>
            </Box>
          )}
        </Box>
      </Grid>
    </Grid>
  );
};
export default ReportFilter;
