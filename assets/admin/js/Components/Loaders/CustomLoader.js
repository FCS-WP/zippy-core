import React from "react";

const CustomLoader = ({ loading = true }) => {
  if (!loading) return null;

  return (
    <div className="customize-loader">
      <div className="loader-wrapper">
        <div className="terminal-loader">
          <div className="terminal-header">
            <div className="terminal-title">Status</div>
            <div className="terminal-controls">
              <div className="control close"></div>
              <div className="control minimize"></div>
              <div className="control maximize"></div>
            </div>
          </div>
          <div className="text">Loading...</div>
        </div>
      </div>
    </div>
  );
};
export default CustomLoader;
