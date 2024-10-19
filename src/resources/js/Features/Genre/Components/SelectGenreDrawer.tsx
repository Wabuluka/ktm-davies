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
  Center,
  FormControl,
  FormLabel,
  IconButton,
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
import { QueryParams, useIndexGenreQuery } from '../Hooks/useIndexGenreQuery';
import { CreateGenreDrawer } from './CreateGenreDrawer';
import { EditGenreDrawer } from './EditGenreDrawer';
import { DragDropContext, Draggable, Droppable } from 'react-beautiful-dnd';
import { Genre } from '@/Features/Genre/Types';
import { useSortGenreMutationDND } from '@/Features/Genre/Hooks/useSortGenreMutationDND';
import { DragHandleIcon } from '@chakra-ui/icons';
import { queryClient } from '@/Lib/react-query';

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

  const sortBlockMutation = useSortGenreMutationDND();
  useEffect(() => {
    queryClient.invalidateQueries(queryKey);
  }, [sortBlockMutation.isSuccess, queryKey]);
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

  const handleSort = (data: Genre) => {
    console.log('am here', data);
    sortBlockMutation.mutate(data);
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
                  <DragDropContext
                    onDragEnd={(result) => {
                      if (!result.destination) return;
                      if (result.destination.index === result.source.index)
                        return;
                      const newBlocks = [...genreList];
                      const [reorderedItem] = newBlocks.splice(
                        result.source.index,
                        1,
                      );
                      newBlocks.splice(
                        result.destination.index,
                        0,
                        reorderedItem,
                      );
                      const payload = newBlocks.map((genre, index) => ({
                        id: genre.id,
                        order: index + 1,
                        name: genre.name,
                        sort: genre.sort,
                      }));
                      handleSort(payload);
                      console.log('sorted', payload);
                    }}
                  >
                    <Droppable droppableId="genre-selection">
                      {(provided) => (
                        <Tbody
                          ref={provided.innerRef}
                          {...provided.droppableProps}
                        >
                          {genreList.map((genre, i) => (
                            <Draggable
                              key={genre.id}
                              index={i}
                              draggableId={genre.id.toString()}
                            >
                              {(provided) => (
                                <Tr
                                  {...provided.draggableProps}
                                  {...provided.dragHandleProps}
                                  ref={provided.innerRef}
                                >
                                  <Td w={1} p={0}>
                                    <Center>
                                      <Radio
                                        p={4}
                                        name="genre"
                                        value={genre.id.toString()}
                                        onChange={() =>
                                          setSelectedGenreIdInDrawer(genre.id)
                                        }
                                        checked={
                                          genre.id === selectedGenreIdInDrawer
                                        }
                                      />
                                      <IconButton
                                        as={DragHandleIcon}
                                        aria-label={``}
                                        bg="gray.500"
                                        color="white"
                                        p={2}
                                      />
                                    </Center>
                                  </Td>
                                  <Td>{genre.name}</Td>
                                  {!queryParams?.name && (
                                    <Td w={1}>
                                      {/* <SortGenreButtons
                                        genreId={genre.id}
                                        first={i === 0}
                                        last={i === genreList.length - 1}
                                      /> */}
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
                              )}
                            </Draggable>
                          ))}
                          {provided.placeholder}
                        </Tbody>
                      )}
                    </Droppable>
                  </DragDropContext>
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
