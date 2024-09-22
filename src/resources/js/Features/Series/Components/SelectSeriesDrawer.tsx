import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';
import { EditButton } from '@/UI/Components/Form/Button/EditButton';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { SearchForm } from '@/UI/Components/Form/Input/SearchForm';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import {
  Box,
  Button,
  ButtonGroup,
  FormControl,
  FormLabel,
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
import { FC, useState } from 'react';
import { QueryParams, useIndexSeriesQuery } from '../Hooks/useIndexSeriesQuery';
import { CreateSeriesDrawer } from './CreateSeriesDrawer';
import { EditSeriesDrawer } from './EditSeriesDrawer';
import { PaginatorBase } from '@/UI/Components/Navigation/PaginatorBase';

type Props = {
  onSubmit: (seriesId?: number) => void;
  selectedSeriesId?: number;
  renderOpenDrawerElement: (onOpen: () => void) => JSX.Element;
};

export const SelectSeriesDrawer: FC<Props> = ({
  onSubmit,
  selectedSeriesId,
  renderOpenDrawerElement,
}) => {
  const { isOpen, onOpen, onClose } = useDisclosure();
  const [name, setName] = useState('');
  const [queryParams, setQueryParams] = useState<QueryParams>();
  const [selectedSeriesIdInDrawer, setSelectedSeriesIdInDrawer] = useState<
    number | null
  >(null);
  const {
    data: seriesPaginator,
    isLoading,
    queryKey,
  } = useIndexSeriesQuery(queryParams);

  const handleClose = () => {
    setName('');
    setQueryParams(undefined);
    setSelectedSeriesIdInDrawer(null);
    onClose();
  };

  const handlePageChange = (page: number) => {
    setSelectedSeriesIdInDrawer(null);
    setQueryParams((prev) => ({ ...prev, page }));
  };

  const handleSelectionSubmit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    e.stopPropagation();

    if (!selectedSeriesIdInDrawer) return;

    onSubmit(selectedSeriesIdInDrawer);
    handleClose();
  };

  const handleSeriesDeleted = (deletedSeries_id: number) => {
    if (!selectedSeriesId || selectedSeriesId !== deletedSeries_id) return;

    onSubmit();
  };

  const handleSearchSubmit = () => {
    setSelectedSeriesIdInDrawer(null);
    setQueryParams({ name });
  };

  return (
    <>
      {renderOpenDrawerElement(onOpen)}
      <Drawer isOpen={isOpen} onClose={handleClose}>
        <Text>Select Series</Text>

        <VStack align="stretch">
          <SearchForm onSubmit={handleSearchSubmit}>
            <FormControl isRequired>
              <FormLabel>Series Name</FormLabel>
              <Input
                type="text"
                name="name"
                value={name}
                onChange={(e) => setName(e.target.value)}
              />
            </FormControl>
          </SearchForm>

          {isLoading ? (
            <LoadingSpinner />
          ) : seriesPaginator?.data ? (
            <form id="series-selection" onSubmit={handleSelectionSubmit}>
              <RadioGroup
                defaultValue={selectedSeriesId?.toString()}
                value={selectedSeriesIdInDrawer?.toString()}
              >
                <Table>
                  <Tbody>
                    {seriesPaginator?.data.map((series) => (
                      <Tr key={series.id}>
                        <Td w={1} p={0}>
                          <Radio
                            p={4}
                            name="series"
                            value={series.id.toString()}
                            onChange={() =>
                              setSelectedSeriesIdInDrawer(series.id)
                            }
                            checked={series.id === selectedSeriesIdInDrawer}
                          />
                        </Td>
                        <Td>{series.name}</Td>
                        <Td w={1}>
                          <EditSeriesDrawer
                            seriesId={series.id}
                            onSeriesDeleted={handleSeriesDeleted}
                            renderOpenDrawerElement={(onOpen) => (
                              <EditButton
                                aria-label={'Edit Series'}
                                onClick={onOpen}
                              />
                            )}
                          />
                        </Td>
                      </Tr>
                    ))}
                  </Tbody>
                </Table>
              </RadioGroup>
              <PaginatorBase
                onPageChange={handlePageChange}
                lastPage={seriesPaginator.last_page}
                currentIndex={seriesPaginator.current_page}
              />
            </form>
          ) : (
            <DataFetchError />
          )}

          <Box>
            <CreateSeriesDrawer
              queryKey={queryKey}
              renderOpenDrawerElement={(onOpen) => (
                <PrimaryButton onClick={onOpen}>Create New</PrimaryButton>
              )}
            />
          </Box>
        </VStack>

        <ButtonGroup>
          <Button variant="outline" onClick={handleClose}>
            Back
          </Button>
          <PrimaryButton
            form="series-selection"
            type="submit"
            isDisabled={!selectedSeriesIdInDrawer}
          >
            Select
          </PrimaryButton>
        </ButtonGroup>
      </Drawer>
    </>
  );
};
