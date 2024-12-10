<?php
enum Role: string
{
  case Administrator = "Administrator";
  case Staff = "Staff";
  case User = "User";
}

enum UnitStatus: string
{
  case Available = "available";
  case Borrowed = "borrowed";
  case Maintenance = "maintenance";
}

enum BorrowStatus: string
{
  case Pending = "pending";
  case Active = "active";
  case Rejected = "rejected";
  case Returned = "returned";
}
