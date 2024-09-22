import Selection from '@/Features/Book/Components/Form/Selection';
import { useBookFormState } from '@/Features/Book/Context/BookFormContext';
import { useSelectCreationTypeDrawer } from '@/Features/CreationType';
import { CreatorSelection } from '@/Features/Creator/Components/CreatorSelection';
import { useSelectCreatorDrawer } from '@/Features/Creator/Hooks/useSelectCreatorDrawer';
import { Creator } from '@/Features/Creator/Types';
import { useTextInput } from '@/Hooks/Form/useTextInput';
import { FormControl, FormLabel, Input, VStack } from '@chakra-ui/react';
import { ComponentProps, useState } from 'react';
import { CreationFormData } from '../Types';

type Props = {
  creation?: CreationFormData;
  onSubmit: (creation: CreationFormData) => void;
  onValid?: () => void;
  onInvalid?: () => void;
} & Omit<ComponentProps<'form'>, 'onSubmit'>;

export function CreationForm({
  creation,
  onSubmit,
  onValid,
  onInvalid,
  children,
  ...props
}: Props) {
  const [formData, setFormData] = useState<CreationFormData>({
    creator_id: creation?.creator_id || '',
    creation_type: creation?.creation_type || '',
    displayed_type: creation?.displayed_type || '',
  });
  const {
    data: { creations },
  } = useBookFormState();
  const [isValid, setIsValid] = useState(
    !!formData.creation_type && !!formData.creator_id,
  );
  const checkValidity = (formData: CreationFormData) => {
    const valid = !!formData.creation_type && !!formData.creator_id;
    setIsValid(valid);
    valid ? onValid?.() : onInvalid?.();
  };
  const displayedTypeInput = {
    value: formData.displayed_type,
    ...useTextInput((displayed_type) =>
      setFormData({ ...formData, displayed_type }),
    ),
  };
  const selectedCreatorIds = creations.map((creation) =>
    Number(creation.creator_id),
  );
  const selectable = (creator: Creator) =>
    !selectedCreatorIds
      .filter((id) => id.toString() !== creation?.creator_id)
      .includes(creator.id);
  const { selectCreatorDrawer, selectCreatorDrawerOpenButton } =
    useSelectCreatorDrawer({
      selectable,
      buttonLabel: 'Select',
      onSubmit: (creator) => {
        setFormData((prev) => {
          const newFormData = { ...prev, creator_id: String(creator.id) };
          checkValidity(newFormData);

          return newFormData;
        });
      },
    });
  const { selectCreationTypeDrawer, selectCreationTypeDrawerOpenButton } =
    useSelectCreationTypeDrawer({
      buttonLabel: 'Select',
      onSubmit: (creation_type) => {
        setFormData((prev) => {
          const newFormData = { ...prev, creation_type };
          checkValidity(newFormData);

          return newFormData;
        });
      },
    });

  function handleUnselectCreator() {
    setFormData((prev) => {
      const newFormData = { ...prev, creator_id: '' };
      checkValidity(newFormData);

      return newFormData;
    });
  }
  function handleUnselectCreationType() {
    setFormData((prev) => {
      const newFormData = { ...prev, creation_type: '' };
      checkValidity(newFormData);

      return newFormData;
    });
  }
  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    e.stopPropagation();
    if (!isValid) {
      return;
    }
    onSubmit(formData);
  }

  return (
    <>
      <form onSubmit={handleSubmit} {...props}>
        <VStack align="stretch" spacing={4}>
          <FormControl isRequired>
            <FormLabel>Creator</FormLabel>
            <Input type="hidden" value={formData.creator_id} />
            {formData.creator_id ? (
              <CreatorSelection
                creatorId={formData.creator_id}
                onUnselect={handleUnselectCreator}
              />
            ) : (
              selectCreatorDrawerOpenButton
            )}
          </FormControl>
          <FormControl isRequired>
            <FormLabel>Creation Type</FormLabel>
            <Input type="hidden" value={formData.creation_type} />
            {formData.creation_type ? (
              <Selection onUnselect={handleUnselectCreationType}>
                {formData.creation_type}
              </Selection>
            ) : (
              selectCreationTypeDrawerOpenButton
            )}
          </FormControl>
          <FormControl>
            <FormLabel>Update Creation Type</FormLabel>
            <Input {...displayedTypeInput} placeholder="Special Thanks" />
          </FormControl>
          {children}
        </VStack>
      </form>
      {selectCreatorDrawer}
      {selectCreationTypeDrawer}
    </>
  );
}
