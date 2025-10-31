import React from "react";
import DatePicker from "react-datepicker";

export default function FilterDateRange({
  fromDate,
  setFromDate,
  toDate,
  setToDate,
}) {
  const setDateRange = (update) => {
    if (update[0]) {
      const from = update[0];
      setFromDate(
        `${from.getFullYear()}-${String(from.getMonth() + 1).padStart(
          2,
          "0"
        )}-${String(from.getDate()).padStart(2, "0")}`
      );
    } else {
      setFromDate("");
    }

    if (update[1]) {
      const to = update[1];
      setToDate(
        `${to.getFullYear()}-${String(to.getMonth() + 1).padStart(
          2,
          "0"
        )}-${String(to.getDate()).padStart(2, "0")}`
      );
    } else {
      setToDate("");
    }
  };

  return (
    <DatePicker
      startDate={fromDate ? new Date(fromDate) : null}
      endDate={toDate ? new Date(toDate) : null}
      onChange={(update) => {
        setDateRange(update);
      }}
      selectsRange
      isClearable
    />
  );
}
