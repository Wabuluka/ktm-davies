import { Benefit } from '@/Features/Benefit';
import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';
import { EditButton } from '@/UI/Components/Form/Button/EditButton';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { SearchForm } from '@/UI/Components/Form/Input/SearchForm';
import { DrawerPagenator } from '@/UI/Components/Navigation/DrawerPaginator';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { createPaginationLinks } from '@/UI/Utils/createPaginationLinks';
import {
  Box,
  Button,
  ButtonGroup,
  FormControl,
  FormLabel,
  Image,
  Input,
  Radio,
  RadioGroup,
  Table,
  Tbody,
  Td,
  Text,
  Tr,
  useDisclosure,
  VStack,
} from '@chakra-ui/react';
import { FC, useEffect, useState } from 'react';
import { useIndexBenefitsQuery } from '@/Features/Benefit/Hooks/useIndexBenefitsQuery';
import { QueryParams } from '@/Features/Label/Hooks/useIndexLabelQuery';
import { EditBenefitDrawer } from '@/Features/Benefit/Components/EditBenefitDrawer';
import { CreateBenefitDrawer } from '@/Features/Benefit/Components/CreateBenefitDrawer';

type Props = {
  onSubmit: (benefit: Benefit) => void;
  onBenefitDeleted: (id: number) => void;
  renderOpenDrawerElement: (onOpen: () => void) => JSX.Element;
};

export const SelectBenefitDrawer: FC<Props> = ({
  onSubmit,
  onBenefitDeleted,
  renderOpenDrawerElement,
}) => {
  const { isOpen, onOpen, onClose } = useDisclosure();
  const [benefit, setBenefit] = useState('');
  const [queryParams, setQueryParams] = useState<QueryParams>();
  const [selectedBenefitInDrawer, setSelectedBenefitInDrawer] =
    useState<Benefit | null>(null);
  const [currentIndex, setCurrentIndex] = useState(0);
  const { benefits, lastPage, isLoading, queryKey } =
    useIndexBenefitsQuery(queryParams);
  const { pagenationLinks } = createPaginationLinks(location.href, lastPage);

  const handleClose = () => {
    setBenefit('');
    setQueryParams(undefined);
    setCurrentIndex(0);
    onClose();
  };

  const handleSelectionSubmit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    e.stopPropagation();
    if (!selectedBenefitInDrawer) return;
    onSubmit(selectedBenefitInDrawer);
    handleClose();
  };

  const handleSearchSubmit = () => {
    setCurrentIndex(0);
    setQueryParams({ name: benefit });
  };

  const handlePaginator = (index: number) => {
    setCurrentIndex(index);
  };

  useEffect(() => {
    setQueryParams((prevParams) => ({
      ...prevParams,
      currentIndex: currentIndex + 1,
    }));
  }, [currentIndex]);

  return (
    <>
      {renderOpenDrawerElement(onOpen)}
      <Drawer isOpen={isOpen} onClose={handleClose}>
        <Text>Select Store Benefit</Text>

        <VStack align="stretch">
          <SearchForm onSubmit={handleSearchSubmit}>
            <FormControl isRequired>
              <FormLabel>Store Benefit Name</FormLabel>
              <Input
                type="text"
                name="name"
                value={benefit}
                onChange={(e) => setBenefit(e.target.value)}
              />
            </FormControl>
          </SearchForm>

          {isLoading ? (
            <LoadingSpinner />
          ) : benefits ? (
            <Box>
              <form id="benefit-selection" onSubmit={handleSelectionSubmit}>
                <RadioGroup value={selectedBenefitInDrawer?.id.toString()}>
                  <Table maxW="60rem" mb={2}>
                    <Tbody>
                      {benefits.map((benefit, i) => (
                        <Tr key={i}>
                          <Td>
                            <Radio
                              name="benefit"
                              value={benefit.id.toString()}
                              onChange={() =>
                                setSelectedBenefitInDrawer(benefit)
                              }
                              checked={
                                benefit.id === selectedBenefitInDrawer?.id
                              }
                            />
                          </Td>
                          <Td>
                            <Image
                              borderRadius="full"
                              maxH={100}
                              src={benefit.thumbnail?.original_url}
                            />
                          </Td>
                          <Td>{benefit.name}</Td>
                          <Td>
                            <EditBenefitDrawer
                              benefit={benefit}
                              onBenefitDeleted={onBenefitDeleted}
                              renderOpenDrawerElement={(onOpen) => (
                                <EditButton
                                  onClick={onOpen}
                                  aria-label="Edit Store Benefit"
                                />
                              )}
                            />
                          </Td>
                        </Tr>
                      ))}
                    </Tbody>
                  </Table>

                  <DrawerPagenator
                    pageChange={handlePaginator}
                    links={pagenationLinks}
                    currentIndex={currentIndex}
                  />
                </RadioGroup>
              </form>
            </Box>
          ) : (
            <DataFetchError />
          )}

          <Box>
            <CreateBenefitDrawer
              queryKey={queryKey}
              renderOpenDrawerElement={(onOpen) => (
                <PrimaryButton onClick={onOpen}>Create</PrimaryButton>
              )}
            />
          </Box>
        </VStack>

        <ButtonGroup>
          <Button variant="outline" onClick={handleClose}>
            Back
          </Button>
          <PrimaryButton
            form="benefit-selection"
            type="submit"
            isDisabled={!selectedBenefitInDrawer}
          >
            Select
          </PrimaryButton>
        </ButtonGroup>
      </Drawer>
    </>
  );
};
