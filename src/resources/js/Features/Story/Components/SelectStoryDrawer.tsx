import { Story } from '@/Features/Story';
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
  Center,
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
import { FC, useEffect, useState } from 'react';
import { EditStoryDrawer } from './EditStoryDrawer';
import { CreateStoryDrawer } from './CreateStoryDrawer';
import { PreviewableThumbnail } from '@/UI/Components/MediaAndIcons/PreviewableThumbnail';
import {
  QueryParams,
  useIndexStoriesQuery,
} from '@/Features/Story/Hooks/useIndexStoriesQuery';

type Props = {
  onSubmit: (story: Story) => void;
  onStoryDeleted: (id: number) => void;
  renderOpenDrawerElement: (onOpen: () => void) => JSX.Element;
};

export const SelectStoryDrawer: FC<Props> = ({
  onSubmit,
  onStoryDeleted,
  renderOpenDrawerElement,
}) => {
  const { isOpen, onOpen, onClose } = useDisclosure();
  const [title, setTitle] = useState('');
  const [queryParams, setQueryParams] = useState<QueryParams>();
  const [selectedStoryInDrawer, setSelectedStoryInDrawer] =
    useState<Story | null>(null);
  const [currentIndex, setCurrentIndex] = useState(0);
  const { stories, lastPage, isLoading, queryKey } =
    useIndexStoriesQuery(queryParams);
  const { pagenationLinks } = createPaginationLinks(location.href, lastPage);

  const handleClose = () => {
    setTitle('');
    setQueryParams(undefined);
    setCurrentIndex(0);
    onClose();
  };

  const handleSelectionSubmit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    e.stopPropagation();
    if (!selectedStoryInDrawer) return;
    onSubmit(selectedStoryInDrawer);
    handleClose();
  };

  const handleSearchSubmit = () => {
    setCurrentIndex(0);
    setQueryParams({ title: title });
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
        <Text>Select Story</Text>

        <VStack align="stretch">
          <SearchForm onSubmit={handleSearchSubmit}>
            <FormControl isRequired>
              <FormLabel>Story Name</FormLabel>
              <Input
                type="text"
                name="name"
                value={title}
                onChange={(e) => setTitle(e.target.value)}
              />
            </FormControl>
          </SearchForm>

          {isLoading ? (
            <LoadingSpinner />
          ) : stories ? (
            <Box>
              <form id="story-selection" onSubmit={handleSelectionSubmit}>
                <RadioGroup value={selectedStoryInDrawer?.id.toString()}>
                  <Table maxW="60rem" mb={2}>
                    <Tbody>
                      {stories.map((story, i) => (
                        <Tr key={i}>
                          <Td>
                            <Radio
                              name="story"
                              value={story.id.toString()}
                              onChange={() => setSelectedStoryInDrawer(story)}
                              checked={story.id === selectedStoryInDrawer?.id}
                            />
                          </Td>
                          <Td p={0}>
                            {!!story.thumbnail && (
                              <Center p={1}>
                                <PreviewableThumbnail
                                  previewTriggerProps={{
                                    'aria-label': 'Preview Thumbnail Image',
                                  }}
                                  imageProps={{
                                    src: story.thumbnail.original_url,
                                    alt: '',
                                  }}
                                />
                              </Center>
                            )}
                          </Td>
                          <Td>{story.title}</Td>
                          <Td>
                            <EditStoryDrawer
                              story={story}
                              onStoryDeleted={onStoryDeleted}
                              renderOpenDrawerElement={(onOpen) => (
                                <EditButton
                                  onClick={onOpen}
                                  aria-label="edit story"
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
            <CreateStoryDrawer
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
            form="story-selection"
            type="submit"
            isDisabled={!selectedStoryInDrawer}
          >
            Select
          </PrimaryButton>
        </ButtonGroup>
      </Drawer>
    </>
  );
};
