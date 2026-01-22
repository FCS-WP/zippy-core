import React from "react";
import {
  Box,
  IconButton,
  MenuItem,
  Select,
  Stack,
  Typography,
} from "@mui/material";
import {
  FirstPage,
  LastPage,
  NavigateBefore,
  NavigateNext,
} from "@mui/icons-material";

const TablePaginationCustom = ({
  count,
  rowsPerPage,
  page,
  onPageChange,
  onRowsPerPageChange,
  rowsPerPageOptions = [5, 10, 25, 50, 100],
}) => {
  const totalPages = Math.ceil(count / rowsPerPage);
  const startCounter = page * rowsPerPage + 1;
  const endCounter = Math.min((page + 1) * rowsPerPage, count);

  const handleFirstPage = () => {
    onPageChange(null, 0);
  };

  const handlePreviousPage = () => {
    if (page > 0) {
      onPageChange(null, page - 1);
    }
  };

  const handleNextPage = () => {
    if (page < totalPages - 1) {
      onPageChange(null, page + 1);
    }
  };

  const handleLastPage = () => {
    onPageChange(null, totalPages - 1);
  };

  return (
    <Box
      display="flex"
      justifyContent="space-between"
      alignItems="center"
      p={2}
      sx={{
        borderTop: "1px solid",
        borderColor: "divider",
        flexDirection: { xs: "column", sm: "row" },
        gap: { xs: 2, sm: 0 },
      }}
    >
      <Stack direction="row" spacing={1} alignItems="center">
        <Typography
          variant="body2"
          sx={{ fontSize: { xs: "0.75rem", sm: "0.875rem" } }}
        >
          Rows per page:
        </Typography>
        <Select
          value={rowsPerPage}
          onChange={onRowsPerPageChange}
          size="small"
          sx={{ minWidth: 70 }}
        >
          {rowsPerPageOptions.map((option) => (
            <MenuItem key={option} value={option}>
              {option}
            </MenuItem>
          ))}
        </Select>
      </Stack>

      <Stack
        direction="row"
        spacing={2}
        alignItems="center"
        sx={{
          flexDirection: { xs: "column", sm: "row" },
          gap: { xs: 1, sm: 2 },
        }}
      >
        <Typography
          variant="body2"
          sx={{ fontSize: { xs: "0.75rem", sm: "0.875rem" } }}
        >
          {startCounter} - {endCounter} of {count}
        </Typography>

        <Stack direction="row" spacing={0.5}>
          <IconButton
            onClick={handleFirstPage}
            disabled={page === 0}
            size="small"
            aria-label="first page"
            sx={{ padding: { xs: "4px", sm: "8px" } }}
          >
            <FirstPage sx={{ fontSize: { xs: "1.2rem", sm: "1.5rem" } }} />
          </IconButton>

          <IconButton
            onClick={handlePreviousPage}
            disabled={page === 0}
            size="small"
            aria-label="previous page"
            sx={{ padding: { xs: "4px", sm: "8px" } }}
          >
            <NavigateBefore sx={{ fontSize: { xs: "1.2rem", sm: "1.5rem" } }} />
          </IconButton>

          <IconButton
            onClick={handleNextPage}
            disabled={page >= totalPages - 1}
            size="small"
            aria-label="next page"
            sx={{ padding: { xs: "4px", sm: "8px" } }}
          >
            <NavigateNext sx={{ fontSize: { xs: "1.2rem", sm: "1.5rem" } }} />
          </IconButton>

          <IconButton
            onClick={handleLastPage}
            disabled={page >= totalPages - 1}
            size="small"
            aria-label="last page"
            sx={{ padding: { xs: "4px", sm: "8px" } }}
          >
            <LastPage sx={{ fontSize: { xs: "1.2rem", sm: "1.5rem" } }} />
          </IconButton>
        </Stack>
      </Stack>
    </Box>
  );
};

export default TablePaginationCustom;
