import React from "react";
import {
  Col,
  Row,
  Form,
  Button,
} from "react-bootstrap";

const WooAuthForm = ({
  handleSubmit,
  callbackUrl,
  returnUrl,
  admin_id,
  ...props
}) => {
  return (
    <Form onSubmit={handleSubmit}>
      <Row>
        <Col sm="12" className="text-center">
          <Button className="mt-5 btn-auth" variant="primary" type="submit">
            Connect
          </Button>
        </Col>
      </Row>
    </Form>
  );
};
export default WooAuthForm;
