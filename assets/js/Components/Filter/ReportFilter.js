import { Dropdown } from "bootstrap";
import React from "react";
import { Button, Col, Row, ButtonGroup } from "react-bootstrap";
import { Badge, DropdownButton, DropdownItem } from "react-bootstrap/esm";

const ReportFilter = ({
  onClickViewType,
  viewTypeSelected,
  onClearDate,
  dateSelected,
  onClick,
  activeFilter,
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
      key: "last_week",
    },
  ];

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

  const handleFilterDate = (key) => {
    onClick(key);
  };

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
                    onClick={() => handleFilterDate(filter.key)}
                    variant={
                      activeFilter === filter.key ? "light active" : "light"
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
                  <Col sm="6" className="d-flex w-100 align-items-center">
                    <span> Currently viewing By:</span>

                    {dateSelected?.name && (
                      <div className="font-weight-bold small">
                        <Badge
                          variant="dark"
                          className="badge-dark inline-block align-items-center ml-2 pl-2 pr-2"
                          onClick={onClearDate}
                        >
                          <h6 c>  {dateSelected.name}</h6>
                          <button
                            className="close-btn"
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
                      title={`By ${viewTypeSelected}`}
                      variant="tranparent"
                      className="float-right "
                    >
                      {viewTypes.map((viewType) => (
                        <DropdownItem
                          onClick={() => onClickViewType(viewType.key)}
                          key={viewType.key}
                        >
                          {viewType.title}
                        </DropdownItem>
                      ))}
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
