import React from "react";
import {
  Col,
  Row,
  Form,
  FormControl,
  FormGroup,
  Button,
} from "react-bootstrap";

const WooAuthForm = ({ handleSubmit, callbackUrl, returnUrl, ...props }) => {
  return (
    <Form onSubmit={handleSubmit}>
      <Col sm="12">
        <FormGroup className="mt-3" controlId="formBasicEmail">
          <FormControl
            readOnly={true}
            hidden
            name="app_name"
            defaultValue="Zippy Core"
            type="text"
            placeholder="Enter your Consumer Key"
          />
          <FormControl
            readOnly={true}
            hidden
            name="scope"
            type="text"
            defaultValue="read_write"
            placeholder="Enter your Consumer Key"
          />
          <FormControl
            readOnly={true}
            hidden
            type="text"
            name="user_id"
            defaultValue={admin_id.userID}
            placeholder="Enter your Consumer Key"
          />
          <FormControl
            readOnly={true}
            hidden
            name="return_url"
            defaultValue={returnUrl}
            type="text"
            placeholder="Enter your Consumer Key"
          />
          <FormControl
            readOnly={true}
            hidden
            name="callback_url"
            defaultValue={callbackUrl}
            type="text"
            placeholder="Enter your Consumer Key"
          />
        </FormGroup>
      </Col>
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
