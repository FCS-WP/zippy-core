import React from "react";
import {
  Badge,
  CardBody,
  Col,
  DropdownButton,
  DropdownItem,
  Row,
} from "react-bootstrap";
const ViewType = ({
  viewTypeSelected,
  onClickViewType,
  dateSelected,
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
    <CardBody className="reportFilter">
      <Row>
        <Col md="6" className="d-flex align-items-center">
          <span> Currently viewing By:</span>

          {dateSelected?.name && (
            <div className="font-weight-bold small">
              <Badge
                variant="dark"
                className="badge-dark inline-block align-items-center ml-2 pl-2 pr-2 d-flex"
                onClick={() => onClearDate(dateSelected.type)}
              >
                <p className="select-bage"> {dateSelected.name}</p>
                <button className="close-btn" aria-label="Clear selected date">
                  ×
                </button>
              </Badge>
            </div>
          )}
        </Col>
        <Col md="6">
          <DropdownButton
            title={`By ${viewTypeSelected}`}
            variant="tranparent"
            className="float-right  "
          >
            {viewTypes.map((viewType) => (
              <DropdownItem
                className="btn-viewby"
                onClick={() => onClickViewType(viewType.key)}
                key={viewType.key}
              >
                {viewType.title}
              </DropdownItem>
            ))}
          </DropdownButton>
        </Col>
      </Row>
    </CardBody>
  );
};
export default ViewType;
