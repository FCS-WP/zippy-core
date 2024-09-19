import { Dropdown } from "bootstrap";
import React, { useState } from "react";
import { Button, Col, Row, ButtonGroup } from "react-bootstrap";
import { Badge, DropdownButton, DropdownItem } from "react-bootstrap/esm";
import DatePicker from "react-datepicker";
import { DateHelper } from "../../helper/date-helper";
const ReportFilter = ({
  onClickViewType,
  viewTypeSelected,
  onClearDate,
  dateSelected,
  onClick,
  activeFilter,
  handleCustomDate,
  ...props
}) => {
  const filters = [
    // {
    //   title: "Year",
    //   key: "year",
    // },
    // {
    //   title: "Last month",
    //   key: "last_month",
    // },
    // {
    //   title: "This Month",
    //   key: "month",
    // },
    {
      title: "Last 7 days",
      key: "last_week",
    },
    {
      title: "Custom",
      key: "custom",
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
    <Row>
      <Col md="12">
        <div className="reportFilter">
          <Row>
            <Col sm="6">
              <ButtonGroup className="mb-0 date-filter-button align-items-center">
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
                {activeFilter === "custom" && (
                  <>
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
                      className="ml-3 go-btn"
                      variant="success"
                      onClick={handleSubmit}
                    >
                      Go
                    </Button>
                  </>
                )}
              </ButtonGroup>
            </Col>
            <Col md="6" className="d-flex align-items-center justify-content-between w-100">
                <Row className="w-100">
                  <Col sm="6" className="d-flex w-100 align-items-center">
                    <span> Currently viewing By:</span>

                    {dateSelected?.name && (
                      <div className="font-weight-bold small">
                        <Badge
                          variant="dark"
                          className="badge-dark inline-block align-items-center ml-2 pl-2 pr-2 d-flex"
                          onClick={onClearDate}
                        >
                          <p className="select-bage"> {dateSelected.name}</p>
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
            </Col>
          </Row>
        </div>
      </Col>
    </Row>
  );
};
export default ReportFilter;
