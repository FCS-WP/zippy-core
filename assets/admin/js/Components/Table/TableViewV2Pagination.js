import {
  Box,
  Button,
  FormControl,
  IconButton,
  InputLabel,
  MenuItem,
  Select,
  Stack,
  Typography,
} from "@mui/material";
import React, { useState } from "react";
import ChevronLeftIcon from "@mui/icons-material/ChevronLeft";
import ChevronRightIcon from "@mui/icons-material/ChevronRight";
import FirstPageIcon from "@mui/icons-material/FirstPage";
import LastPageIcon from "@mui/icons-material/LastPage";

const TableViewV2Pagination = (props) => {
  const {
    rowsPerPage = 5,
    currentPage = 1,
    totalRows,
    onChangeRowsPerPage,
    onChangePage,
  } = props;

  const startCounter = (currentPage - 1) * rowsPerPage + 1;
  const endCounter =
    totalRows > currentPage * rowsPerPage
      ? currentPage * rowsPerPage
      : totalRows;
  const totalPages = Math.ceil(totalRows / rowsPerPage);

  const getShowPages = (currentPage, totalPages) => {
    if (totalPages <= 3) return [1, 2, 3].slice(0, totalPages);
    if (currentPage <= 2) return [1, 2, "...", totalPages];
    if (currentPage >= totalPages - 1)
      return [1, "...", totalPages - 1, totalPages];
    return [currentPage - 1, currentPage, currentPage + 1];
  };

  const PaginateButtons = () => {
    const pages = getShowPages(8, totalPages);
    console.log(totalPages);
    return (
      <Box display="flex" gap={1}>
        {pages.map((pageNumber, i) =>
          pageNumber === "..." ? (
            <Box display={"flex"} alignItems={"end"} key={i} px={2}>
              ...
            </Box>
          ) : (
            <Button
              key={pageNumber}
              sx={{ minWidth: "auto !important" }}
              variant={pageNumber === currentPage ? "contained" : "outlined"}
              onClick={() => onChangePage(pageNumber)}
            >
              {pageNumber}
            </Button>
          )
        )}
      </Box>
    );
  };

  return (
    <Box>
      <Box
        display={"flex"}
        justifyContent={"space-between"}
        alignItems={"center"}
        p={2}
      >
        <Box>
          <Typography variant="body1">
            {startCounter} - {endCounter} of {totalRows}
          </Typography>
        </Box>
        <Box>
          <FormControl fullWidth sx={{ minWidth: 150 }}>
            <InputLabel id="select-rows">Rows per page</InputLabel>
            <Select
              size="small"
              labelId="select-rows"
              id="custom-select"
              value={rowsPerPage}
              label="Rows per page"
              onChange={(e) => onChangeRowsPerPage(e.target.value)}
            >
              <MenuItem value={5} defaultValue={rowsPerPage == 5}>
                5
              </MenuItem>
              <MenuItem value={10} defaultValue={rowsPerPage == 10}>
                10
              </MenuItem>
              <MenuItem value={20} defaultValue={rowsPerPage == 15}>
                15
              </MenuItem>
              <MenuItem value={30} defaultValue={rowsPerPage == 20}>
                20
              </MenuItem>
            </Select>
          </FormControl>
        </Box>
      </Box>
      <Box display={"flex"} justifyContent={"end"} p={2} gap={1} pt={0}>
        <IconButton
          disabled={currentPage === 1}
          onClick={() => onChangePage(1)}
        >
          <FirstPageIcon />
        </IconButton>
        <IconButton onClick={() => onChangePage(currentPage - 1)}>
          <ChevronLeftIcon />
        </IconButton>
        <PaginateButtons />
        <IconButton onClick={() => onChangePage(currentPage + 1)}>
          <ChevronRightIcon />
        </IconButton>
        <IconButton
          disabled={currentPage === totalPages}
          onClick={() => onChangePage(totalPages)}
        >
          <LastPageIcon />
        </IconButton>
      </Box>
    </Box>
  );
};

export default TableViewV2Pagination;
