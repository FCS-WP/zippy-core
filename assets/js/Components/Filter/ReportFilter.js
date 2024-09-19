import { Dropdown } from "bootstrap";
import React from "react";
import { Button, Col, Row, ButtonGroup } from "react-bootstrap";
import { Badge, DropdownButton, DropdownItem } from "react-bootstrap/esm";
const ReportFilter = ({
  onClearDate,
  dateSelected,
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
    <Row>
      <Col sm="12">
        <div className="reportFilter">
          <Row>
            <Col sm="8">
              <ButtonGroup className="mb-0 date-filter-button">
                {filters.map((filter) => (
                  <Button
                    key={filter.key}
                    onClick={() => onClick(filter.key)}
                    variant={
                      activeFilter === filter.key
                        ? "light active"
                        : buttonVariant
                    }
                  >
                    {filter.title}
                  </Button>
                ))}

                <Button variant="light">Custom:</Button>
              </ButtonGroup>
            </Col>
            <Col sm="4">
              <div>
                <Row>
                  <Col sm="6" className="d-flex w-100">
                    {dateSelected && (
                      <div className="font-weight-bold small">
                        <span> You are currently viewing:</span>
                        <Badge
                          variant="dark"
                          className="badge-dark inline-block align-items-center ml-2 pl-2 pr-2"
                        >
                          {dateSelected}
                          <button
                            className="close-btn"
                            onClick={onClearDate}
                            aria-label="Clear selected date"
                          >
                            ×
                          </button>
                        </Badge>
                      </div>
                    )}
                  </Col>
                  <Col sm="6">
                    <DropdownButton
                      title="By day"
                      variant="tranparent"
                      className="float-right "
                    >
                      <DropdownItem href="#/action-1">By Day</DropdownItem>
                      <DropdownItem href="#/action-1">By Week</DropdownItem>
                      <DropdownItem href="#/action-1">By Month</DropdownItem>
                    </DropdownButton>
                  </Col>
                </Row>
              </div>
            </Col>
          </Row>
        </div>
      </Col>
    </Row>
  );
};
export default ReportFilter;
