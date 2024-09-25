import React from "react";
import Arrows from "../../../icons/back.svg";
import { Col, Row } from "react-bootstrap";

const AuthTitle = () => {
  return (
    <>
      <Row className="mb-5">
        <Col>
          <div className="authen-thumnail">
            <img src="/wp-content/plugins/zippy-core/assets/images/logo-zippy.png"></img>
            <Arrows />
            <img src="/wp-content/plugins/zippy-core/assets/images/woocommerce.png"></img>
          </div>
        </Col>
      </Row>
      <Row>
        <Col>
          <h4 className="text-center my-2">
            Authentication with Woocommerce to see Order Analytics
          </h4>
        </Col>
      </Row>
    </>
  );
};

export default AuthTitle;
