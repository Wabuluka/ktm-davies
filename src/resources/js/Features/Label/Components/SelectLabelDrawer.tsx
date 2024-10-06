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
import { QueryParams, useIndexLabelQuery } from '../Hooks/useIndexLabelQuery';
import { CreateLabelDrawer } from './CreateLabelDrawer';
import { EditLabelDrawer } from './EditLabelDrawer';
import { DragDropContext, Draggable, Droppable } from 'react-beautiful-dnd';
import { useSortLabelMutationDND } from '@/Features/Label/Hooks/useSortLabelMutationDND';
import { Label } from '@/Features/Label/Types';

type Props = {
  onSubmit: (labelId?: number) => void;
  selectedLabelId?: number;
  renderOpenDrawerElement: (onOpen: () => void) => JSX.Element;
};

export const SelectLabelDrawer: FC<Props> = ({
  onSubmit,
  selectedLabelId,
  renderOpenDrawerElement,
}) => {
  const { isOpen, onOpen, onClose } = useDisclosure();
  const [name, setName] = useState('');
  const [queryParams, setQueryParams] = useState<QueryParams>();
  const [selectedLabelIdInDrawer, setSelectedLabelIdInDrawer] = useState<
    number | null
  >(null);
  const {
    data: labelList,
    isLoading,
    queryKey,
  } = useIndexLabelQuery(queryParams);
  const sortLabelMutation = useSortLabelMutationDND();

  const handleClose = () => {
    setName('');
    setQueryParams(undefined);
    setSelectedLabelIdInDrawer(null);
    onClose();
  };

  const handleSelectionSubmit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    e.stopPropagation();

    if (!selectedLabelIdInDrawer) return;

    onSubmit(selectedLabelIdInDrawer);
    handleClose();
  };

  const handleLabelDeleted = (deletedLabel_id: number) => {
    if (!selectedLabelId || selectedLabelId !== deletedLabel_id) return;

    onSubmit();
  };

  const handleSearchSubmit = () => {
    setQueryParams({ name: name });
  };

  const handleSort = (data: Label[]) => {
    sortLabelMutation.mutate(data);
  };

  return (
    <>
      {renderOpenDrawerElement(onOpen)}
      <Drawer isOpen={isOpen} onClose={handleClose}>
        <Text>Select Label</Text>

        <VStack align="stretch">
          <SearchForm onSubmit={handleSearchSubmit}>
            <FormControl isRequired>
              <FormLabel>Label Name</FormLabel>
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
          ) : labelList ? (
            <form id="label-selection" onSubmit={handleSelectionSubmit}>
              <RadioGroup
                defaultValue={selectedLabelId?.toString()}
                value={selectedLabelIdInDrawer?.toString()}
              >
                <Table>
                  <DragDropContext
                      onDragEnd={(result) => {
                        if (!result.destination) return;
                        if (result.destination.index === result.source.index) return;
                        const newLabels = Array.from(labelList);
                        const [reorderedItem] = newLabels.splice(result.source.index, 1);
                        newLabels.splice(result.destination.index, 0, reorderedItem);
                        const payload = newLabels.map((label, index) => ({
                          id: label.id,
                          order: index + 1,
                          name: label.name,
                          url: '',
                          genre_id: label.genre_id,
                          types: [
                            {
                              id: label.id,
                              sort: index + 1,
                              name: label.name,
                              is_digital: false
                            }
                          ]
                        }));
                        handleSort(payload);
                      }}
                    >
                    <Droppable droppableId='labels-1'>
                      {(provided) => (
                        <Tbody ref={provided.innerRef} {...provided.droppableProps}>
                          {labelList.map((label, index) => (
                            <Draggable key={label.id} index={index} draggableId={label.id.toString()}>
                              {(provided) => (
                                <Tr
                                  key={label.id}
                                  ref={provided.innerRef}
                                  {...provided.draggableProps}
                                  {...provided.dragHandleProps}
                                  style={{
                                    ...provided.draggableProps.style,
                                    padding: "10px",
                                    margin: "5px 0",
                                    border: "1px solid #ddd",
                                    borderRadius: "4px"
                                  }}
                                >
                                  <Td w={1} p={0}>
                                    <Radio
                                      p={4}
                                      name="label"
                                      value={label.id.toString()}
                                      onChange={() =>
                                        setSelectedLabelIdInDrawer(label.id)
                                      }
                                      isChecked={label.id === selectedLabelIdInDrawer}
                                    />
                                  </Td>
                                  <Td>{label.name}</Td>
                                  {!queryParams?.name && (
                                    <Td w={1}>
                                      {/* Placeholder for SortLabelButtons if needed */}
                                    </Td>
                                  )}
                                  <Td w={1}>
                                    <EditLabelDrawer
                                      labelId={label.id}
                                      onLabelDeleted={handleLabelDeleted}
                                      renderOpenDrawerElement={(onOpen) => (
                                        <EditButton
                                          aria-label={'Edit Label'}
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
            <CreateLabelDrawer
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
            form="label-selection"
            type="submit"
            isDisabled={!selectedLabelIdInDrawer}
          >
            Select
          </PrimaryButton>
        </ButtonGroup>
      </Drawer>
    </>
  );
};
