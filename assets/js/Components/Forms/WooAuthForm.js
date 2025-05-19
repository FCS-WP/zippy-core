import React from "react";

const WooAuthForm = ({
  handleSubmit,
  callbackUrl,
  returnUrl,
  admin_id,
  ...props
}) => {
  return (
    <div>Woo Auth Form</div>
    // <Row>
    //   <Col sm="12" className="text-center">
    //     <Button
    //       onClick={handleSubmit}
    //       className="mt-5 btn-auth"
    //       variant="primary"
    //       type="submit"
    //     >
    //       Connect
    //     </Button>
    //   </Col>
    // </Row>
  );
};
export default WooAuthForm;
