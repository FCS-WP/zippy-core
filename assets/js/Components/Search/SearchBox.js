import {
  Box,
  Button,
  Chip,
  Grid,
  IconButton,
  InputAdornment,
  List,
  ListItemButton,
  ListItemText,
  TextField,
  Tooltip,
} from "@mui/material";
import React, { useCallback, useEffect, useState } from "react";
import {
  ChipContainer,
  SearchContainer,
  StyledPaper,
  SuggestionsContainer,
} from "../custom-mui/CustomLayouts";
import { debounce } from "../../helper/debounce";
import SearchIcon from "@mui/icons-material/Search";
import { toast } from "react-toastify";
import { Api } from "../../api";

const SearchBox = ({ includeCategories, update }) => {

  const [categorySearch, setCategorySearch] = useState("");
  const [selectedCategories, setSelectedCategories] = useState([]);
  const [filteredCategories, setFilteredCategories] = useState([]);
  const [isLoading, setIsLoading] = useState(false);

  const handleSearch = async (keyword) => {
    const params = {
      keyword: keyword,
    };

    const response = await Api.searchCategories(params);
    return response?.data.results;
  };

  const debounceFetchCategories = useCallback(
    debounce(async (keyword) => {
      if (keyword.trim()) {
        const dataProducts = await handleSearch(keyword);
        if (dataProducts) {
          setFilteredCategories(dataProducts);
        } else {
          toast.error("Search error");
          setFilteredCategories([]);
        }
      } else {
        setFilteredCategories([]);
      }
    }, 500),
    []
  );

  useEffect(() => {
    debounceFetchCategories(categorySearch);
  }, [categorySearch]);

  const handleCategoryClick = (categories) => {
    const isInSelectedArr = selectedCategories.find(
      (item) => item.id === categories.id
    );
    if (!isInSelectedArr) {
      setSelectedCategories([...selectedCategories, categories]);
    }
    setCategorySearch("");
  };

  const handleProductKeyDown = (event) => {
    if (event.key === "Enter" && filteredCategories.length > 0) {
      handleCategoryClick(filteredCategories[0]);
      event.preventDefault();
    }
  };

  const handleDeleteCategory = (productToDelete) => {
    setSelectedCategories(
      selectedCategories.filter((product) => product !== productToDelete)
    );
  };

  useEffect(()=>{
    setSelectedCategories(includeCategories);
  }, [includeCategories])

  return (
    <Box>
      <StyledPaper>
        <SearchContainer>
          <TextField
            fullWidth
            size="small"
            label="Search category"
            variant="outlined"
            placeholder="Type to search..."
            value={categorySearch}
            onChange={(e) => setCategorySearch(e.target.value)}
            onKeyDown={handleProductKeyDown}
            slotProps={{
              input: {
                startAdornment: (
                  <InputAdornment position="start">
                    <SearchIcon />
                  </InputAdornment>
                ),
              },
            }}
          />
          {categorySearch && (
            <SuggestionsContainer>
              <List>
                {filteredCategories.length > 0 ? (
                  filteredCategories.map((product, index) => (
                    <ListItemButton
                      key={index}
                      divider={index !== filteredCategories.length - 1}
                      onClick={() => handleCategoryClick(product)}
                    >
                      <ListItemText primary={product.name} />
                    </ListItemButton>
                  ))
                ) : (
                  <ListItemButton>
                    <ListItemText
                      primary="No products found"
                      sx={{ color: "text.secondary" }}
                    />
                  </ListItemButton>
                )}
              </List>
            </SuggestionsContainer>
          )}
        </SearchContainer>
        <Grid>
          <ChipContainer>
            {selectedCategories.map((category, index) => (
              <Chip
                key={index}
                label={category.name}
                onDelete={() => handleDeleteCategory(category)}
              />
            ))}
          </ChipContainer>
        </Grid>
      </StyledPaper>
    </Box>
  );
};

export default SearchBox;
