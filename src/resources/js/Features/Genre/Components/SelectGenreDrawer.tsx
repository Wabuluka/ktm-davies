import { SortGenreButtons } from '@/Features/Genre/Components/SortGenreButtons';
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
import { QueryParams, useIndexGenreQuery } from '../Hooks/useIndexGenreQuery';
import { CreateGenreDrawer } from './CreateGenreDrawer';
import { EditGenreDrawer } from './EditGenreDrawer';

type Props = {
  onSubmit: (GenreId?: number) => void;
  selectedGenreId?: number;
  renderOpenDrawerElement: (onOpen: () => void) => JSX.Element;
};

export const SelectGenreDrawer: FC<Props> = ({
  onSubmit,
  selectedGenreId,
  renderOpenDrawerElement,
}) => {
  const { isOpen, onOpen, onClose } = useDisclosure();
  const [name, setName] = useState('');
  const [queryParams, setQueryParams] = useState<QueryParams>();
  const [selectedGenreIdInDrawer, setSelectedGenreIdInDrawer] = useState<
    number | null
  >(null);
  const {
    data: genreList,
    isLoading,
    queryKey,
  } = useIndexGenreQuery(queryParams);

  const handleClose = () => {
    setName('');
    setQueryParams(undefined);
    setSelectedGenreIdInDrawer(null);
    onClose();
  };

  const handleSelectionSubmit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    e.stopPropagation();

    if (!selectedGenreIdInDrawer) return;

    onSubmit(selectedGenreIdInDrawer);
    handleClose();
  };

  const handleGenreDeleted = (deletedGenre_id: number) => {
    if (!selectedGenreId || selectedGenreId !== deletedGenre_id) return;

    onSubmit();
  };

  const handleSearchSubmit = () => {
    setQueryParams({ name: name });
  };

  return (
    <>
      {renderOpenDrawerElement(onOpen)}
      <Drawer isOpen={isOpen} onClose={handleClose}>
        <Text>Select Genre</Text>

        <VStack align="stretch">
          <SearchForm onSubmit={handleSearchSubmit}>
            <FormControl isRequired>
              <FormLabel>Genre Name</FormLabel>
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
          ) : genreList ? (
            <form id="genre-selection" onSubmit={handleSelectionSubmit}>
              <RadioGroup
                defaultValue={selectedGenreId?.toString()}
                value={selectedGenreIdInDrawer?.toString()}
              >
                <Table>

                  <Tbody>
                    {genreList.map((genre, i) => (
                      <Tr key={genre.id}>
                        <Td w={1} p={0}>
                          <Radio
                            p={4}
                            name="genre"
                            value={genre.id.toString()}
                            onChange={() =>
                              setSelectedGenreIdInDrawer(genre.id)
                            }
                            checked={genre.id === selectedGenreIdInDrawer}
                          />
                        </Td>
                        <Td>{genre.name}</Td>
                        {!queryParams?.name && (
                          <Td w={1}>
                            <SortGenreButtons
                              genreId={genre.id}
                              first={i === 0}
                              last={i === genreList.length - 1}
                            />
                          </Td>
                        )}
                        <Td w={1}>
                          <EditGenreDrawer
                            genreId={genre.id}
                            onGenreDeleted={handleGenreDeleted}
                            renderOpenDrawerElement={(onOpen) => (
                              <EditButton
                                aria-label={'Edit Genre'}
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
            </form>
          ) : (
            <DataFetchError />
          )}

          <Box>
            <CreateGenreDrawer
              queryKey={queryKey}
              renderOpenDrawerElement={(onOpen) => (
                <PrimaryButton onClick={onOpen}>Create new</PrimaryButton>
              )}
            />
          </Box>
        </VStack>

        <ButtonGroup>
          <Button variant="outline" onClick={handleClose}>
            Back
          </Button>
          <PrimaryButton
            form="genre-selection"
            type="submit"
            isDisabled={!selectedGenreIdInDrawer}
          >
            Select
          </PrimaryButton>
        </ButtonGroup>
      </Drawer>
    </>
  );
};
