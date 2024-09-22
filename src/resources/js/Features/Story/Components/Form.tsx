import {
  Button,
  FormControl,
  FormErrorMessage,
  FormLabel,
  Input,
  VStack,
  useDisclosure,
} from '@chakra-ui/react';
import { ComponentProps, FC, useState } from 'react';
import { FileInput } from '@/UI/Components/Form/Input/FileInput';
import { Story, StoryFormData } from '@/Features/Story';
import { useTextInput } from '@/Hooks/Form/useTextInput';
import { ImagePreview } from '@/UI/Components/MediaAndIcons/ImagePreview';
import { CreatorList } from '@/Features/Story/Components/CreatorList';
import { Creator, SelectCreatorDrawer } from '@/Features/Creator';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';

type Props = {
  story?: Story;
  initialFocusRef?: React.LegacyRef<HTMLInputElement>;
  errors?: Record<string, string[]>;
  onSubmit: (formData: StoryFormData) => void;
} & Omit<ComponentProps<'form'>, 'onSubmit'>;

export const Form: FC<Props> = ({
  story,
  errors,
  initialFocusRef,
  onSubmit,
  ...props
}) => {
  const [uploadedThumbnail, setUploadedThumbnail] = useState(story?.thumbnail);
  const { isOpen, onClose, onOpen } = useDisclosure();

  const [formData, setFormData] = useState<StoryFormData>(
    story
      ? {
          title: story.title,
          trial_url: story.trial_url || '',
          creators: story.creators.map((creator) => ({
            id: `${creator.id}`,
            sort: creator.pivot.sort,
          })),
          thumbnail: {
            operation: 'stay',
          },
        }
      : {
          title: '',
          trial_url: '',
          creators: [],
          thumbnail: {
            operation: 'stay',
          },
        },
  );

  const titleInput = {
    value: formData.title,
    ...useTextInput((title) => setFormData({ ...formData, title })),
  };
  const trialUrlInput = {
    value: formData.trial_url,
    ...useTextInput((trial_url) => setFormData({ ...formData, trial_url })),
  };

  function isSelectableCreator(creator: Creator): boolean {
    return !formData.creators.some((c) => c.id === `${creator.id}`);
  }

  function handleCreatorAdd(creator: Creator) {
    setFormData({
      ...formData,
      creators: [
        ...formData.creators,
        { id: `${creator.id}`, sort: formData.creators.length + 1 },
      ],
    });
  }

  function handleCreatorRemove(creatorId: string) {
    const creators = formData.creators
      .filter((creator) => creator.id !== creatorId)
      .map((creator, i) => ({ ...creator, sort: i + 1 }));
    setFormData({ ...formData, creators });
  }

  function handleCreatorOrderDown(creatorId: string) {
    const newCreators = formData.creators;
    const index = formData.creators.findIndex(
      (creator) => creator.id === creatorId,
    );
    if (index === -1 || index === formData.creators.length - 1) {
      throw new Error('index is -1 or last');
    }
    const from = newCreators[index];
    const to = newCreators[index + 1];
    newCreators.splice(
      index,
      2,
      { ...to, sort: from.sort },
      { ...from, sort: to.sort },
    );
    setFormData({ ...formData, creators: newCreators });
  }

  function handleCreatorOrderUp(creatorId: string) {
    const newCreators = formData.creators;
    const index = formData.creators.findIndex(
      (creator) => creator.id === creatorId,
    );
    if (index <= 0) {
      throw new Error('index is 0 or less');
    }
    const from = newCreators[index];
    const to = newCreators[index - 1];
    newCreators.splice(
      index - 1,
      2,
      { ...from, sort: to.sort },
      { ...to, sort: from.sort },
    );
    setFormData({ ...formData, creators: newCreators });
  }

  function handleThumbnailChange(file: File | null) {
    setFormData({
      ...formData,
      thumbnail: file ? { operation: 'set', file } : { operation: 'stay' },
    });
  }
  function handleThumbnailUnselect() {
    setFormData({ ...formData, thumbnail: { operation: 'delete' } });
    setUploadedThumbnail(undefined);
  }

  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    e.stopPropagation();
    onSubmit(formData);
  }

  return (
    <form {...props} onSubmit={handleSubmit}>
      <VStack align="stretch" spacing={4}>
        <FormControl isInvalid={!!errors} isRequired>
          <FormLabel>Story Name</FormLabel>
          <Input ref={initialFocusRef} {...titleInput} />
          {errors?.title?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
          {errors?.story?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
        </FormControl>

        <FormControl isInvalid={!!errors}>
          <FormLabel>Trial URL</FormLabel>
          <Input {...trialUrlInput} />
          {errors?.url?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
          {errors?.story?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
        </FormControl>

        <FormControl>
          <FormLabel>Creator Information</FormLabel>
          <CreatorList
            creators={formData.creators}
            onCreatorRemove={handleCreatorRemove}
            onCreatorOrderDown={handleCreatorOrderDown}
            onCreatorOrderUp={handleCreatorOrderUp}
            bg="gray.100"
          />
          {errors?.creators?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
        </FormControl>

        <SelectCreatorDrawer
          isOpen={isOpen}
          onClose={onClose}
          selectable={isSelectableCreator}
          onSubmit={handleCreatorAdd}
        />
        <PrimaryButton onClick={onOpen}>Add a Creator</PrimaryButton>

        <FormControl>
          <FormLabel>Thumbnail</FormLabel>
          {uploadedThumbnail?.original_url ? (
            <ImagePreview
              src={uploadedThumbnail?.original_url}
              alt="chosen thumbnail"
            >
              <Button onClick={handleThumbnailUnselect}>Deselect</Button>
            </ImagePreview>
          ) : (
            <FileInput
              accept="image/*"
              alt="chosen thumbnail"
              onChange={handleThumbnailChange}
            />
          )}
          {errors?.['thumbnail.operation']?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
          {errors?.['thumbnail.file']?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
        </FormControl>
      </VStack>
    </form>
  );
};
