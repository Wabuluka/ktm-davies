import {
  useBookFormState,
  useSetBookFormData,
} from '@/Features/Book/Context/BookFormContext';
import { Story } from '@/Features/Story';
import { EditButton } from '@/UI/Components/Form/Button/EditButton';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { DeleteIcon } from '@chakra-ui/icons';
import {
  Box,
  Button,
  ButtonGroup,
  FormControl,
  FormLabel,
  HStack,
  IconButton,
  Table,
  Tbody,
  Td,
  Text,
  Tr,
  VStack,
} from '@chakra-ui/react';
import { FC, useState } from 'react';
import { EditStoryDrawer } from './EditStoryDrawer';
import { SelectStoryDrawer } from './SelectStoryDrawer';
import { StorySelection } from './StorySelection';

type Props = {
  isOpen: boolean;
  onClose: () => void;
};

export const StoryDrawer: FC<Props> = ({ isOpen, onClose }) => {
  const { data } = useBookFormState();
  const initialStories = data?.stories || [];
  const [stories, setStories] = useState<Story[]>(initialStories);
  const { setData } = useSetBookFormData();

  const handleClose = () => {
    const storiesData = data?.stories || [];
    setStories(storiesData);
    onClose();
  };

  const handleSelect = (story: Story) => {
    setStories((prev) => {
      if (prev && prev.find((s) => s.id === story.id)) {
        return prev;
      } else {
        return prev ? [...prev, story] : [story];
      }
    });
  };

  const handleUpdate = (story: Story) => {
    setStories((prev) => {
      return (
        prev?.map((s) =>
          s.id === story.id
            ? {
                ...s,
                title: story.title,
                trial_url: story.trial_url,
                thumbnail: story.thumbnail,
                creators: story.creators,
              }
            : s,
        ) ?? null
      );
    });
  };

  const handleDelete = (storyId: number): void => {
    setStories((prev) => prev.filter((item) => item.id !== storyId));
  };
  const handleDeleteByMutation = (storyId: number): void => {
    handleDelete(storyId);
    setData((prev) => ({
      ...prev,
      stories: prev.stories?.filter((item) => item.id !== storyId),
    }));
  };

  function handleSubmit() {
    setData('stories', stories);
    onClose();
  }

  return (
    <Drawer isOpen={isOpen} onClose={handleClose}>
      <Text>Add Story</Text>

      <VStack align="stretch">
        <Box>
          <FormControl isRequired>
            <FormLabel>Story</FormLabel>
          </FormControl>

          <HStack spacing={8}>
            <Table maxW="60rem" mb={2}>
              <Tbody>
                {stories.map((story, i) => (
                  <Tr key={i}>
                    <Td>
                      <StorySelection
                        storyId={story.id}
                        handleUpdate={handleUpdate}
                      />
                    </Td>
                    <Td>
                      <EditStoryDrawer
                        story={story}
                        onStoryDeleted={handleDeleteByMutation}
                        renderOpenDrawerElement={(onOpen) => (
                          <EditButton
                            onClick={onOpen}
                            aria-label="edit story"
                          />
                        )}
                      />
                    </Td>
                    <Td
                      w={18}
                      sx={{
                        '.chakra-button': {
                          bg: 'red.500',
                          color: 'white',
                          p: 2,
                        },
                      }}
                    >
                      <IconButton
                        as={DeleteIcon}
                        aria-label="delete story"
                        onClick={() => handleDelete(story.id)}
                      />
                    </Td>
                  </Tr>
                ))}
              </Tbody>
            </Table>
          </HStack>

          <SelectStoryDrawer
            onSubmit={(story: Story) => {
              handleSelect(story);
            }}
            onStoryDeleted={handleDeleteByMutation}
            renderOpenDrawerElement={(onOpen) => (
              <PrimaryButton onClick={onOpen} aria-label="select story">
                Add
              </PrimaryButton>
            )}
          />
        </Box>
      </VStack>

      <ButtonGroup>
        <Button variant="outline" onClick={handleClose}>
          Back
        </Button>
        <PrimaryButton onClick={handleSubmit}>Save</PrimaryButton>
      </ButtonGroup>
    </Drawer>
  );
};
