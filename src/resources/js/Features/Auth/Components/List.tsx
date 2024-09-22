import DeleteUsersForm from '@/Features/Auth/Components/DeleteUsersForm';
import {
  LinkBox,
  LinkOverlay,
  Table,
  TableContainer,
  Tbody,
  Td,
  Th,
  Thead,
  Tr,
} from '@chakra-ui/react';
import { User } from '../Types';

type Props = {
  users: User[];
};

export const List = ({ users }: Props) => {
  return (
    <TableContainer>
      <Table>
        <Thead>
          <Tr>
            <Th>ID</Th>
            <Th>Name</Th>
            <Th>Email</Th>
            <Th>Created at</Th>
            <Th>Updated at</Th>
            <Th>Action</Th>
          </Tr>
        </Thead>
        <Tbody>
          {users.map((user, i) => (
            <LinkBox
              as="tr"
              key={i}
              cursor="pointer"
              _hover={{
                bg: 'gray.100',
              }}
            >
              <Td>{user.id}</Td>
              <Td>
                <LinkOverlay href={route('users.edit', { id: user.id })}>
                  {user.name}
                </LinkOverlay>
              </Td>
              <Td>{user.email}</Td>
              <Td>{user.created_at}</Td>
              <Td>{user.updated_at}</Td>
              <Td>
                <DeleteUsersForm userId={user.id} username={user.name} />
              </Td>
            </LinkBox>
          ))}
        </Tbody>
      </Table>
    </TableContainer>
  );
};

export default List;
