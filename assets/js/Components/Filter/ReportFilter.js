import React from "react";
import { Button, Col, Row, ButtonGroup } from "react-bootstrap";
import { CardBody } from "react-bootstrap/esm";
const ReportFilter = ({
  onClick,
  activeFilter,
  buttonVariant = "light",
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
      key: "month",
    },
    {
      title: "Last 7 days",
      key: "week",
    },
  ];
  return (
    <div className="reportFilter">
      <ButtonGroup className="mb-0 date-filter-button">
        {filters.map((filter) => (
          <Button
            key={filter.key}
            onClick={() => onClick(filter.key)}
            variant={
              activeFilter === filter.key ? "light active" : buttonVariant
            }
          >
            {filter.title}
          </Button>
        ))}

        <Button variant="light">Custom:</Button>
      </ButtonGroup>
    </div>
  );
};
export default ReportFilter;
