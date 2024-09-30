import React, { useState, useEffect } from "react";
import {
  Col,
  Row,
  Form,
  FormControl,
  FormGroup,
  FormLabel,
  FormText,
  Button,
  Container,
} from "react-bootstrap";
import AuthTitle from "../Title/AuthTitle";
import { Woocommerce } from "../../Woocommerce/woocommerce";
const AuthContent = () => {
  const [htmlAuth, setHtmlAuth] = useState();

  const domain = window.location.host;
  const return_url = window.location.href;
  const callback_url =
    window.location.origin + "/wp-json/zippy-core/v1/credentials";
  console.log(domain);
  const fetchCredentials = async (params) => {
    const container = document.getElementById("zippy-content");

    const { data } = await Woocommerce.wooAuthentication(params);
    setHtmlAuth(data);
  };
  useEffect(() => {
    console.log(return_url);
  }, [htmlAuth]);

  const handleSubmit = (event) => {
    event.preventDefault();

    if (typeof admin_id == "undefined") return;

    const params = {
      app_name: "Zippy Core",
      scope: "read_write",
      user_id: admin_id,
      return_url: return_url,
      callback_url: callback_url,
    };
    console.log("oke go!");

    fetchCredentials(params);
  };

  return (
    <div id="zippy-content">
      {!htmlAuth ? (
        <div className="content-wrapper">
          <Container className="w-80">
            <AuthTitle />

            <Form onSubmit={handleSubmit}>
              <Col sm="12">
                <FormGroup className="mt-3" controlId="formBasicEmail">
                  <FormControl
                    readOnly="true"
                    hidden
                    name="app_name"
                    defaultValue="Zippy Core"
                    type="text"
                    placeholder="Enter your Consumer Key"
                  />
                  <FormControl
                    readOnly="true"
                    hidden
                    name="scope"
                    type="text"
                    defaultValue="read_write"
                    placeholder="Enter your Consumer Key"
                  />
                  <FormControl
                    readOnly="true"
                    hidden
                    type="text"
                    name="user_id"
                    defaultValue={admin_id.userID}
                    placeholder="Enter your Consumer Key"
                  />
                  <FormControl
                    readOnly="true"
                    hidden
                    name="return_url"
                    defaultValue={return_url}
                    type="text"
                    placeholder="Enter your Consumer Key"
                  />
                  <FormControl
                    readOnly="true"
                    hidden
                    name="callback_url"
                    defaultValue={callback_url}
                    type="text"
                    placeholder="Enter your Consumer Key"
                  />
                </FormGroup>
              </Col>
              <Row>
                <Col sm="12" className="text-center">
                  <Button
                    className="mt-5 btn-auth"
                    variant="primary"
                    type="submit"
                  >
                    Connect
                  </Button>
                </Col>
              </Row>
            </Form>
          </Container>
        </div>
      ) : (
        <div className="content-wrapper">
          <div
            className="container"
            dangerouslySetInnerHTML={{ __html: htmlAuth }}
          />
        </div>
      )}
    </div>
  );
};
export default AuthContent;
