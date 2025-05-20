import React, { useState } from 'react';
import {
  DataGrid,
  GridActionsCellItem,
} from '@mui/x-data-grid';
import {
  Box,
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  Button,
  TextField,
} from '@mui/material';


export default function DataTable(props) {
  const { initRows, columns } = props;
  const [selectionModel, setSelectionModel] = useState([]);

  return (
    <Box sx={{ maxHeight: 600, width: '100%' }} className="custom-data-table">
      <DataGrid
        minHeight={300}
        rows={initRows}
        columns={columns}
        initialState={{
          pagination: {
            paginationModel: {
              pageSize: 5,
            },
          },
        }}
        pageSizeOptions={[5, 10, 25, 50, 100]}
        checkboxSelection
        disableRowSelectionOnClick
        selectionModel={selectionModel}
        onSelectionModelChange={(newSelection) => {
          setSelectionModel(newSelection);
        }}
      />

      {/* Optional: Display selected IDs */}
      {selectionModel.length > 0 && (
        <Box sx={{ mt: 2 }}>
          <strong>Selected Row IDs:</strong> {selectionModel.join(', ')}
        </Box>
      )}
    </Box>
  );
}
