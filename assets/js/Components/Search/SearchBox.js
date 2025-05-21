import {
  Box,
  Button,
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
import HelpOutlineIcon from "@mui/icons-material/HelpOutline";
import AddIcon from "@mui/icons-material/Add";
import { toast } from "react-toastify";

const SearchBox = () => {
  const [productSearch, setProductSearch] = useState("");
  const [selectedProducts, setSelectedProducts] = useState([]);
  const [filteredProducts, setFilteredProducts] = useState([]);
  const [isLoading, setIsLoading] = useState(false);

  const debounceFetchProducts = useCallback(
    debounce(async (keyword, type) => {
      // if (keyword.trim()) {
      //   const dataProducts = await handleSearch(keyword);
      //   if (dataProducts) {
      //     setFilteredProducts(dataProducts);
      //   } else {
      //     toast.error("Search error");
      //     setFilteredProducts([]);
      //   }
      // } else {
      //   setFilteredProducts([]);
      // }
      console.log("Search", keyword);
    }, 500),
    []
  );

  useEffect(() => {
    debounceFetchProducts(productSearch, "product");
  }, [productSearch]);

  const handleProductClick = (product) => {
    const isInSelectedArr = selectedProducts.find(
      (item) => item.id === product.id
    );
    if (!isInSelectedArr) {
      setSelectedProducts([...selectedProducts, product]);
    }
    setProductSearch("");
  };

  const handleProductKeyDown = (event) => {
    if (event.key === "Enter" && filteredProducts.length > 0) {
      handleProductClick(filteredProducts[0]);
      event.preventDefault();
    }
  };

  const handleDeleteProduct = (productToDelete) => {
    setSelectedProducts(
      selectedProducts.filter((product) => product !== productToDelete)
    );
  };

  const handleAddProducts = async () => {
    setIsLoading(true);

    if (selectedProducts.length <= 0) {
      toast.error("Select product first!");
      setIsLoading(false);
      return false;
    }

    try {
      const addedIds = [];
      const prepareRequestData = selectedProducts.map((item) => {
        const productInfo = {
          items_id: item.id,
          mapping_type: "product",
        };
        addedIds.push(productInfo);
      });
      const params = {
        products: addedIds,
      };
      const { data } = await Api.addSupportProducts(params);

      if (!data) {
        setIsLoading(false);
        toast.error("Can not add categories!");
        return false;
      }

      if (data.status != "success") {
        setIsLoading(false);
        toast.error(data.message);
        return false;
      }

      toast.success("Add products successfully!");
      setSelectedProducts([]);
      updateListMapping();
    } catch (error) {
      toast.error("Can not add products!");
    }
    setTimeout(() => {
      setIsLoading(false);
    }, 500);
  };

  const tooltipAddProducts = `The products you add will be supported for booking.`;

  return (
    <Box>
      <StyledPaper>
        <SearchContainer>
          <TextField
            fullWidth
            label="Search Products"
            variant="outlined"
            placeholder="Type to search..."
            value={productSearch}
            onChange={(e) => setProductSearch(e.target.value)}
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
          {productSearch && (
            <SuggestionsContainer>
              <List>
                {filteredProducts.length > 0 ? (
                  filteredProducts.map((product, index) => (
                    <ListItemButton
                      key={index}
                      divider={index !== filteredProducts.length - 1}
                      onClick={() => handleProductClick(product)}
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
        <Grid container spacing={3} alignItems={"end"}>
          <Grid
            display={"flex"}
            justifyContent={"end"}
            textAlign={"end"}
            alignItems={"center"}
            gap={1}
          >
            <Button
              className="btn-hover-float"
              disabled={isLoading}
              sx={{ fontSize: "12px" }}
              onClick={handleAddProducts}
              variant="contained"
              startIcon={<AddIcon />}
            >
              Add Products
            </Button>
            <Tooltip title={tooltipAddProducts}>
              <IconButton size="small" sx={{ p: 0, mb: 0.5 }}>
                <HelpOutlineIcon role="button" />
              </IconButton>
            </Tooltip>
          </Grid>
        </Grid>
      </StyledPaper>
      <Grid>
        <ChipContainer>
          {selectedProducts.map((product, index) => (
            <Chip
              key={index}
              label={product.name}
              onDelete={() => handleDeleteProduct(product)}
            />
          ))}
        </ChipContainer>
      </Grid>
    </Box>
  );
};

export default SearchBox;
